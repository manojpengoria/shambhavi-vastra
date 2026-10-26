<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\CourierPartner;
use App\Models\PostalCode;
use App\Models\ShippingClass;
use App\Models\ShippingMethod;
use App\Models\ShippingPackage;
use App\Models\ShippingSetting;
use App\Models\ShippingZone;
use App\Models\State;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ShippingManagementTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    private State $rajasthan;

    private City $jaipur;

    private PostalCode $jaipurGpo;

    private Area $malviyaNagar;

    private Warehouse $mainWarehouse;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::create([
            'name' => 'Shipping Manager',
            'email' => 'shipping-manager@example.com',
            'password' => 'password',
            'is_active' => true,
        ]);

        $modules = ['warehouses', 'courier-partners', 'shipping-methods', 'shipping-zones', 'shipping-packages', 'shipping-classes'];
        foreach ($modules as $module) {
            foreach (['view', 'create', 'edit', 'delete'] as $ability) {
                $this->grant("{$module}.{$ability}");
            }
        }
        $this->grant('shipping-settings.view');
        $this->grant('shipping-settings.edit');

        $india = Country::create([
            'country_name' => 'India',
            'country_code' => 'IN',
            'dial_code' => '+91',
        ]);
        $this->rajasthan = State::create([
            'country_id' => $india->id,
            'state_name' => 'Rajasthan',
            'state_code' => 'IN-RJ',
        ]);
        $this->jaipur = City::create([
            'state_id' => $this->rajasthan->id,
            'city_name' => 'Jaipur',
            'city_code' => 'JAI',
        ]);
        $this->jaipurGpo = PostalCode::create([
            'city_id' => $this->jaipur->id,
            'postal_code' => '302001',
            'post_office_name' => 'Jaipur GPO',
        ]);
        $this->malviyaNagar = Area::create([
            'city_id' => $this->jaipur->id,
            'postal_code_id' => $this->jaipurGpo->id,
            'area_name' => 'Malviya Nagar',
            'area_code' => 'JAI-MLV',
        ]);
        $this->mainWarehouse = Warehouse::create([
            'name' => 'Main godown',
            'code' => 'WH-JPR',
            'contact_person' => 'Store Manager',
            'mobile' => '9999999999',
            'address_line' => 'Shop 12',
            'state_id' => $this->rajasthan->id,
            'city_id' => $this->jaipur->id,
            'postal_code_id' => $this->jaipurGpo->id,
            'is_default' => true,
            'is_active' => true,
        ]);
    }

    private function grant(string $permissionName): void
    {
        $this->admin->givePermissionTo(
            Permission::findOrCreate($permissionName, 'admin')
        );
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @return array<string, mixed>
     */
    private function warehousePayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Second godown',
            'code' => 'wh-jpr-2',
            'contact_person' => 'Dispatch Lead',
            'mobile' => '9876543210',
            'email' => 'dispatch@example.com',
            'address_line' => 'Plot 7, Industrial Area',
            'state_id' => $this->rajasthan->id,
            'city_id' => $this->jaipur->id,
            'postal_code_id' => $this->jaipurGpo->id,
            'area_id' => $this->malviyaNagar->id,
            'gstin' => '08abcde1234f1z5',
            'pickup_time_slot' => '10 AM - 2 PM',
            'is_default' => '0',
            'is_active' => '1',
        ], $overrides);
    }

    public function test_shipping_list_and_form_pages_render(): void
    {
        $courierPartner = CourierPartner::create(['name' => 'Delhivery', 'code' => 'DLV']);
        $shippingMethod = ShippingMethod::create(['name' => 'Standard', 'code' => 'STD']);
        $shippingZone = ShippingZone::create(['name' => 'Local Jaipur', 'code' => 'LOCAL-JPR', 'coverage_type' => 'city']);
        $shippingZone->cities()->sync([$this->jaipur->id]);
        $shippingPackage = ShippingPackage::create(['name' => 'Medium box', 'code' => 'BOX-M', 'length' => 30, 'breadth' => 20, 'height' => 10]);
        $shippingClass = ShippingClass::create(['name' => 'Heavy', 'code' => 'HEAVY', 'extra_charge' => 50]);

        $pages = [
            'admin.warehouses' => ['Main godown', $this->mainWarehouse],
            'admin.courier-partners' => ['Delhivery', $courierPartner],
            'admin.shipping-methods' => ['Standard', $shippingMethod],
            'admin.shipping-zones' => ['Local Jaipur', $shippingZone],
            'admin.shipping-packages' => ['Medium box', $shippingPackage],
            'admin.shipping-classes' => ['Heavy', $shippingClass],
        ];

        foreach ($pages as $routePrefix => [$expectedText, $model]) {
            $this->actingAs($this->admin, 'admin')
                ->get(route("{$routePrefix}.index"))
                ->assertOk()
                ->assertSee($expectedText);

            $this->actingAs($this->admin, 'admin')
                ->get(route("{$routePrefix}.create"))
                ->assertOk();

            $this->actingAs($this->admin, 'admin')
                ->get(route("{$routePrefix}.edit", $model))
                ->assertOk()
                ->assertSee($expectedText);
        }

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.shipping-zones.edit', $shippingZone))
            ->assertSee('<option value="'.$this->jaipur->id.'" selected>Jaipur (Rajasthan)</option>', false);

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.shipping-settings.edit'))
            ->assertOk()
            ->assertSee('Main godown (WH-JPR)');
    }

    public function test_shipping_pages_require_permission(): void
    {
        $adminWithoutPermissions = Admin::create([
            'name' => 'Order Staff',
            'email' => 'order-staff@example.com',
            'password' => 'password',
            'is_active' => true,
        ]);

        $this->actingAs($adminWithoutPermissions, 'admin')
            ->get(route('admin.warehouses.index'))
            ->assertForbidden();

        $this->actingAs($adminWithoutPermissions, 'admin')
            ->put(route('admin.shipping-settings.update'), [])
            ->assertForbidden();

        $this->actingAs($adminWithoutPermissions, 'admin')
            ->get(route('admin.locations.lookup.areas', ['city_id' => $this->jaipur->id]))
            ->assertForbidden();
    }

    public function test_warehouse_is_created_with_normalized_codes(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.warehouses.store'), $this->warehousePayload())
            ->assertRedirect(route('admin.warehouses.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('warehouses', [
            'code' => 'WH-JPR-2',
            'gstin' => '08ABCDE1234F1Z5',
            'area_id' => $this->malviyaNagar->id,
            'is_default' => false,
        ]);
        $this->assertTrue($this->mainWarehouse->fresh()->is_default);
    }

    public function test_warehouse_rejects_city_outside_selected_state(): void
    {
        $maharashtra = State::create([
            'country_id' => $this->rajasthan->country_id,
            'state_name' => 'Maharashtra',
            'state_code' => 'IN-MH',
        ]);

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.warehouses.store'), $this->warehousePayload(['state_id' => $maharashtra->id]))
            ->assertSessionHasErrors('city_id');

        $this->assertDatabaseMissing('warehouses', ['code' => 'WH-JPR-2']);
    }

    public function test_new_default_warehouse_replaces_previous_default_and_updates_settings(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.warehouses.store'), $this->warehousePayload(['is_default' => '1']))
            ->assertSessionHasNoErrors();

        $newWarehouse = Warehouse::where('code', 'WH-JPR-2')->firstOrFail();

        $this->assertTrue($newWarehouse->is_default);
        $this->assertFalse($this->mainWarehouse->fresh()->is_default);
        $this->assertSame($newWarehouse->id, ShippingSetting::query()->find(1)->default_warehouse_id);
    }

    public function test_default_warehouse_must_be_active_and_cannot_be_deleted(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.warehouses.store'), $this->warehousePayload(['is_default' => '1', 'is_active' => '0']))
            ->assertSessionHasErrors('is_active');

        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.warehouses.destroy', $this->mainWarehouse))
            ->assertRedirect(route('admin.warehouses.index'))
            ->assertSessionHas('error');

        $this->assertModelExists($this->mainWarehouse);
    }

    public function test_courier_partner_logo_is_stored_and_blank_credentials_keep_saved_values(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.courier-partners.store'), [
                'name' => 'Delhivery',
                'code' => 'dlv',
                'logo' => UploadedFile::fake()->image('delhivery.png'),
                'integration_type' => CourierPartner::TYPE_API,
                'api_key' => 'secret-key',
                'api_secret' => 'secret-value',
                'tracking_url' => 'https://www.delhivery.com/track/package/{AWB}',
                'priority' => 1,
                'supports_cod' => '1',
                'supports_reverse_pickup' => '0',
                'supports_express' => '1',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.courier-partners.index'))
            ->assertSessionHasNoErrors();

        $courierPartner = CourierPartner::where('code', 'DLV')->firstOrFail();
        Storage::disk('public')->assertExists($courierPartner->logo);
        $this->assertSame('secret-key', $courierPartner->api_key);

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.courier-partners.update', $courierPartner), [
                'name' => 'Delhivery Express',
                'code' => 'DLV',
                'integration_type' => CourierPartner::TYPE_API,
                'api_key' => '',
                'api_secret' => '',
                'tracking_url' => 'https://www.delhivery.com/track/package/{AWB}',
                'priority' => 2,
                'supports_cod' => '1',
                'supports_reverse_pickup' => '1',
                'supports_express' => '1',
                'is_active' => '1',
            ])
            ->assertSessionHasNoErrors();

        $courierPartner->refresh();
        $this->assertSame('Delhivery Express', $courierPartner->name);
        $this->assertSame('secret-key', $courierPartner->api_key);
        $this->assertSame('secret-value', $courierPartner->api_secret);
        $this->assertTrue($courierPartner->supports_reverse_pickup);
    }

    public function test_courier_partner_requires_api_key_for_api_integration_and_awb_placeholder(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.courier-partners.store'), [
                'name' => 'Blue Dart',
                'code' => 'BLD',
                'integration_type' => CourierPartner::TYPE_API,
                'tracking_url' => 'https://www.bluedart.com/track',
                'priority' => 1,
                'supports_cod' => '1',
                'supports_reverse_pickup' => '0',
                'supports_express' => '0',
                'is_active' => '1',
            ])
            ->assertSessionHasErrors(['api_key', 'tracking_url']);
    }

    public function test_shipping_zone_syncs_only_the_selected_coverage(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.shipping-zones.store'), [
                'name' => 'Rest of Rajasthan',
                'code' => 'rest-rj',
                'coverage_type' => ShippingZone::COVERAGE_STATE,
                'state_ids' => [$this->rajasthan->id],
                'city_ids' => [$this->jaipur->id],
                'priority' => 2,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.shipping-zones.index'))
            ->assertSessionHasNoErrors();

        $shippingZone = ShippingZone::where('code', 'REST-RJ')->firstOrFail();
        $this->assertSame([$this->rajasthan->id], $shippingZone->states()->pluck('states.id')->all());
        $this->assertSame(0, $shippingZone->cities()->count());

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.shipping-zones.update', $shippingZone), [
                'name' => 'Rest of India',
                'code' => 'REST-RJ',
                'coverage_type' => ShippingZone::COVERAGE_ALL,
                'priority' => 9,
                'is_active' => '1',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(0, $shippingZone->states()->count());
    }

    public function test_shipping_zone_requires_states_for_state_coverage(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.shipping-zones.store'), [
                'name' => 'Empty zone',
                'code' => 'EMPTY',
                'coverage_type' => ShippingZone::COVERAGE_STATE,
                'priority' => 1,
                'is_active' => '1',
            ])
            ->assertSessionHasErrors('state_ids');
    }

    public function test_simple_shipping_masters_can_be_created(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.shipping-methods.store'), [
                'name' => 'Express',
                'code' => 'exp',
                'customer_text' => 'Delivered in 1-2 days',
                'display_order' => 2,
                'is_active' => '1',
            ])
            ->assertSessionHasNoErrors();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.shipping-packages.store'), [
                'name' => 'Small box',
                'code' => 'box-s',
                'length' => 20,
                'breadth' => 15,
                'height' => 10,
                'box_weight' => 0.1,
                'max_weight' => 2,
                'cost' => 12.5,
                'is_active' => '1',
            ])
            ->assertSessionHasNoErrors();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.shipping-classes.store'), [
                'name' => 'Fragile',
                'code' => 'fragile',
                'extra_charge' => 40,
                'is_active' => '0',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('shipping_methods', ['code' => 'EXP', 'display_order' => 2]);
        $this->assertDatabaseHas('shipping_packages', ['code' => 'BOX-S']);
        $this->assertDatabaseHas('shipping_classes', ['code' => 'FRAGILE', 'is_active' => false]);
    }

    public function test_shipping_settings_update_and_sync_default_warehouse(): void
    {
        $secondWarehouse = Warehouse::create([
            'name' => 'Second godown',
            'code' => 'WH-2',
            'contact_person' => 'Dispatch Lead',
            'mobile' => '9876543210',
            'address_line' => 'Plot 7',
            'state_id' => $this->rajasthan->id,
            'city_id' => $this->jaipur->id,
            'postal_code_id' => $this->jaipurGpo->id,
            'is_active' => true,
        ]);

        $payload = [
            'default_warehouse_id' => $secondWarehouse->id,
            'default_courier_partner_id' => '',
            'volumetric_divisor' => 4000,
            'free_shipping_above' => 999,
            'cod_enabled' => '1',
            'cod_min_order' => '',
            'cod_max_order' => 5000,
            'return_window_days' => 10,
            'return_shipping_paid_by' => ShippingSetting::PAID_BY_SELLER,
            'show_delivery_date' => '1',
            'pincode_check_before_cart' => '0',
        ];

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.shipping-settings.update'), $payload)
            ->assertRedirect(route('admin.shipping-settings.edit'))
            ->assertSessionHasNoErrors();

        $settings = ShippingSetting::query()->find(1);
        $this->assertSame(4000, $settings->volumetric_divisor);
        $this->assertFalse($settings->pincode_check_before_cart);
        $this->assertTrue($secondWarehouse->fresh()->is_default);
        $this->assertFalse($this->mainWarehouse->fresh()->is_default);

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.shipping-settings.update'), [...$payload, 'cod_min_order' => 6000])
            ->assertSessionHasErrors('cod_max_order');
    }

    public function test_area_lookup_returns_areas_of_the_given_city(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->getJson(route('admin.locations.lookup.areas', ['city_id' => $this->jaipur->id]))
            ->assertOk()
            ->assertExactJson([
                'results' => [
                    ['id' => $this->malviyaNagar->id, 'text' => 'Malviya Nagar (JAI-MLV)'],
                ],
                'pagination' => ['more' => false],
            ]);
    }

    public function test_current_settings_survive_a_serializing_cache_store(): void
    {
        config([
            'cache.stores.array.serialize' => true,
            'cache.serializable_classes' => false,
        ]);
        Cache::forgetDriver('array');

        ShippingSetting::query()->create(['id' => 1, 'volumetric_divisor' => 4500]);

        $this->assertSame(4500, ShippingSetting::current()->volumetric_divisor);

        $cachedSettings = ShippingSetting::current();
        $this->assertInstanceOf(ShippingSetting::class, $cachedSettings);
        $this->assertTrue($cachedSettings->exists);

        $cachedSettings->update(['volumetric_divisor' => 6000]);

        $this->assertSame(6000, ShippingSetting::current()->volumetric_divisor);
    }
}
