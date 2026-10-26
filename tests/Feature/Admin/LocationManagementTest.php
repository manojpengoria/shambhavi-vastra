<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\PostalCode;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class LocationManagementTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    private Country $india;

    private State $rajasthan;

    private City $jaipur;

    private PostalCode $jaipurGpo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::create([
            'name' => 'Location Manager',
            'email' => 'location-manager@example.com',
            'password' => 'password',
            'is_active' => true,
        ]);

        foreach (['countries', 'states', 'cities', 'postal-codes', 'areas'] as $module) {
            foreach (['view', 'create', 'edit', 'delete'] as $ability) {
                $this->admin->givePermissionTo(
                    Permission::create(['name' => "{$module}.{$ability}", 'guard_name' => 'admin'])
                );
            }
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->india = Country::create([
            'country_name' => 'India',
            'country_code' => 'IN',
            'dial_code' => '+91',
            'status' => Country::STATUS_ENABLED,
        ]);
        $this->rajasthan = State::create([
            'country_id' => $this->india->id,
            'state_name' => 'Rajasthan',
            'state_code' => 'IN-RJ',
            'gst_state_code' => '08',
            'state_type' => State::TYPE_STATE,
            'status' => State::STATUS_ENABLED,
        ]);
        $this->jaipur = City::create([
            'state_id' => $this->rajasthan->id,
            'city_name' => 'Jaipur',
            'city_code' => 'JAI',
            'city_type' => City::TYPE_CITY,
            'status' => City::STATUS_ENABLED,
        ]);
        $this->jaipurGpo = PostalCode::create([
            'city_id' => $this->jaipur->id,
            'postal_code' => '302001',
            'post_office_name' => 'Jaipur GPO',
            'office_type' => 'HO',
            'status' => PostalCode::STATUS_ENABLED,
        ]);
        Area::create([
            'city_id' => $this->jaipur->id,
            'postal_code_id' => $this->jaipurGpo->id,
            'area_name' => 'Malviya Nagar',
            'area_code' => 'JAI-MLV',
            'status' => Area::STATUS_ENABLED,
        ]);
    }

    public function test_location_list_and_form_pages_render(): void
    {
        $pages = [
            'admin.countries' => ['India', 'country' => $this->india],
            'admin.states' => ['Rajasthan', 'state' => $this->rajasthan],
            'admin.cities' => ['Jaipur', 'city' => $this->jaipur],
            'admin.postal-codes' => ['302001', 'postal_code' => $this->jaipurGpo],
            'admin.areas' => ['Malviya Nagar', 'area' => Area::first()],
        ];

        foreach ($pages as $routePrefix => $page) {
            $expectedText = array_shift($page);

            $this->actingAs($this->admin, 'admin')
                ->get(route("{$routePrefix}.index"))
                ->assertOk()
                ->assertSee($expectedText);

            $this->actingAs($this->admin, 'admin')
                ->get(route("{$routePrefix}.create"))
                ->assertOk();

            $this->actingAs($this->admin, 'admin')
                ->get(route("{$routePrefix}.edit", $page))
                ->assertOk()
                ->assertSee($expectedText);
        }
    }

    public function test_state_list_can_be_filtered_by_country(): void
    {
        $nepal = Country::create([
            'country_name' => 'Nepal',
            'country_code' => 'NP',
            'dial_code' => '+977',
            'status' => Country::STATUS_ENABLED,
        ]);
        State::create([
            'country_id' => $nepal->id,
            'state_name' => 'Bagmati',
            'state_code' => 'NP-P3',
            'status' => State::STATUS_ENABLED,
        ]);

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.states.index', ['country_id' => $nepal->id]))
            ->assertOk()
            ->assertSee('Bagmati')
            ->assertDontSee('IN-RJ');
    }

    public function test_country_is_created_with_uppercased_code(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.countries.store'), [
                'country_name' => 'Nepal',
                'country_code' => 'np',
                'dial_code' => '+977',
                'status' => Country::STATUS_ENABLED,
            ])
            ->assertRedirect(route('admin.countries.index'));

        $this->assertDatabaseHas('countries', [
            'country_name' => 'Nepal',
            'country_code' => 'NP',
        ]);
    }

    public function test_country_code_must_be_unique(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->from(route('admin.countries.create'))
            ->post(route('admin.countries.store'), [
                'country_name' => 'Another India',
                'country_code' => 'in',
                'dial_code' => '+91',
                'status' => Country::STATUS_ENABLED,
            ])
            ->assertRedirect(route('admin.countries.create'))
            ->assertSessionHasErrors('country_code');

        $this->assertSame(1, Country::count());
    }

    public function test_country_with_states_cannot_be_deleted(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.countries.destroy', $this->india))
            ->assertRedirect(route('admin.countries.index'))
            ->assertSessionHas('error');

        $this->assertModelExists($this->india);
    }

    public function test_state_can_be_updated_keeping_its_own_unique_codes(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.states.update', $this->rajasthan), [
                'country_id' => $this->india->id,
                'state_name' => 'Rajasthan State',
                'state_code' => 'IN-RJ',
                'gst_state_code' => '08',
                'state_type' => State::TYPE_STATE,
                'capital' => 'Jaipur',
                'status' => State::STATUS_ENABLED,
                'sort_order' => 3,
            ])
            ->assertRedirect(route('admin.states.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame('Rajasthan State', $this->rajasthan->fresh()->state_name);
        $this->assertSame('Jaipur', $this->rajasthan->fresh()->capital);
    }

    public function test_city_cannot_be_created_under_a_disabled_state(): void
    {
        $disabledState = State::create([
            'country_id' => $this->india->id,
            'state_name' => 'Goa',
            'state_code' => 'IN-GA',
            'status' => State::STATUS_DISABLED,
        ]);

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.cities.store'), [
                'state_id' => $disabledState->id,
                'city_name' => 'Panaji',
                'city_code' => 'PNJ',
                'city_type' => City::TYPE_CITY,
                'status' => City::STATUS_ENABLED,
                'sort_order' => 0,
            ])
            ->assertSessionHasErrors('state_id');

        $this->assertDatabaseMissing('cities', ['city_code' => 'PNJ']);
    }

    public function test_city_with_postal_codes_cannot_be_deleted(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.cities.destroy', $this->jaipur))
            ->assertRedirect(route('admin.cities.index'))
            ->assertSessionHas('error');

        $this->assertModelExists($this->jaipur);
    }

    public function test_postal_code_and_post_office_must_be_unique_within_a_city(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.postal-codes.store'), [
                'city_id' => $this->jaipur->id,
                'postal_code' => '302001',
                'post_office_name' => 'Jaipur GPO',
                'office_type' => 'HO',
                'delivery_status' => PostalCode::DELIVERY_AVAILABLE,
                'status' => PostalCode::STATUS_ENABLED,
            ])
            ->assertSessionHasErrors('postal_code');

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.postal-codes.store'), [
                'city_id' => $this->jaipur->id,
                'postal_code' => '302001',
                'post_office_name' => 'Johari Bazar',
                'office_type' => 'SO',
                'delivery_status' => PostalCode::DELIVERY_AVAILABLE,
                'status' => PostalCode::STATUS_ENABLED,
            ])
            ->assertRedirect(route('admin.postal-codes.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame(2, PostalCode::where('postal_code', '302001')->count());
    }

    public function test_area_is_created_with_postal_code_from_its_city(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.areas.store'), [
                'city_id' => $this->jaipur->id,
                'postal_code_id' => $this->jaipurGpo->id,
                'area_name' => 'Vaishali Nagar',
                'area_code' => 'jai-vsn',
                'area_type' => Area::TYPE_LOCALITY,
                'latitude' => '26.9118',
                'longitude' => '75.7430',
                'delivery_available' => Area::DELIVERY_AVAILABLE,
                'status' => Area::STATUS_ENABLED,
                'sort_order' => 1,
            ])
            ->assertRedirect(route('admin.areas.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('areas', [
            'area_name' => 'Vaishali Nagar',
            'area_code' => 'JAI-VSN',
            'postal_code_id' => $this->jaipurGpo->id,
        ]);
    }

    public function test_area_rejects_postal_code_from_another_city(): void
    {
        $ajmer = City::create([
            'state_id' => $this->rajasthan->id,
            'city_name' => 'Ajmer',
            'city_code' => 'AJM',
            'status' => City::STATUS_ENABLED,
        ]);

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.areas.store'), [
                'city_id' => $ajmer->id,
                'postal_code_id' => $this->jaipurGpo->id,
                'area_name' => 'Vaishali Nagar',
                'area_code' => 'AJM-VSN',
                'area_type' => Area::TYPE_LOCALITY,
                'delivery_available' => Area::DELIVERY_AVAILABLE,
                'status' => Area::STATUS_ENABLED,
                'sort_order' => 0,
            ])
            ->assertSessionHasErrors('postal_code_id');

        $this->assertDatabaseMissing('areas', ['area_code' => 'AJM-VSN']);
    }

    public function test_location_pages_require_permission(): void
    {
        $adminWithoutPermissions = Admin::create([
            'name' => 'Order Staff',
            'email' => 'order-staff@example.com',
            'password' => 'password',
            'is_active' => true,
        ]);

        $this->actingAs($adminWithoutPermissions, 'admin')
            ->get(route('admin.countries.index'))
            ->assertForbidden();

        $this->actingAs($adminWithoutPermissions, 'admin')
            ->delete(route('admin.areas.destroy', Area::first()))
            ->assertForbidden();

        $this->actingAs($adminWithoutPermissions, 'admin')
            ->get(route('admin.locations.lookup.cities'))
            ->assertForbidden();

        $this->assertSame(1, Area::count());
    }

    public function test_city_lookup_returns_enabled_cities_matching_search(): void
    {
        City::create([
            'state_id' => $this->rajasthan->id,
            'city_name' => 'Jaisalmer',
            'city_code' => 'JSM',
            'status' => City::STATUS_DISABLED,
        ]);
        City::create([
            'state_id' => $this->rajasthan->id,
            'city_name' => 'Udaipur',
            'city_code' => 'UDR',
            'status' => City::STATUS_ENABLED,
        ]);

        $this->actingAs($this->admin, 'admin')
            ->getJson(route('admin.locations.lookup.cities', ['q' => 'jai']))
            ->assertOk()
            ->assertExactJson([
                'results' => [
                    ['id' => $this->jaipur->id, 'text' => 'Jaipur (Rajasthan)'],
                ],
                'pagination' => ['more' => false],
            ]);

        $this->actingAs($this->admin, 'admin')
            ->getJson(route('admin.locations.lookup.cities', ['q' => 'jai', 'include_disabled' => 1]))
            ->assertOk()
            ->assertJsonCount(2, 'results');
    }

    public function test_postal_code_lookup_is_scoped_to_the_given_city(): void
    {
        $ajmer = City::create([
            'state_id' => $this->rajasthan->id,
            'city_name' => 'Ajmer',
            'city_code' => 'AJM',
            'status' => City::STATUS_ENABLED,
        ]);
        PostalCode::create([
            'city_id' => $ajmer->id,
            'postal_code' => '305001',
            'post_office_name' => 'Ajmer HO',
            'status' => PostalCode::STATUS_ENABLED,
        ]);

        $this->actingAs($this->admin, 'admin')
            ->getJson(route('admin.locations.lookup.postal-codes', ['city_id' => $this->jaipur->id]))
            ->assertOk()
            ->assertExactJson([
                'results' => [
                    ['id' => $this->jaipurGpo->id, 'text' => '302001 - Jaipur GPO'],
                ],
                'pagination' => ['more' => false],
            ]);

        $this->actingAs($this->admin, 'admin')
            ->getJson(route('admin.locations.lookup.postal-codes'))
            ->assertUnprocessable();
    }

    public function test_area_edit_form_preselects_current_city_and_postal_code(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.areas.edit', Area::first()))
            ->assertOk()
            ->assertSee('<option value="'.$this->jaipur->id.'" selected>Jaipur (Rajasthan)</option>', false)
            ->assertSee('<option value="'.$this->jaipurGpo->id.'" selected>302001 - Jaipur GPO</option>', false);
    }
}
