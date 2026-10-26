<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PaymentOptionControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<int, string>  $permissions
     */
    private function admin(array $permissions = ['payment-options.view', 'payment-options.edit']): Admin
    {
        $admin = Admin::create([
            'name' => 'Store Admin',
            'email' => 'store-admin@example.com',
            'password' => 'password',
            'is_active' => true,
        ]);

        foreach ($permissions as $permission) {
            $admin->givePermissionTo(Permission::findOrCreate($permission, 'admin'));
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $admin;
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function razorpayPayload(array $overrides = []): array
    {
        return array_merge([
            'payment_method' => 'razorpay',
            'intent' => 'activate',
            'key' => 'rzp_test_key',
            'merchant_name' => 'Shambhavi Vastra',
            'mode' => 'test',
            'secret_key' => 'rzp-secret-value',
            'min_order_amount' => '100',
            'max_order_amount' => '50000',
            'display_options' => ['visa', 'upi'],
            'instructions' => 'Pay securely with Razorpay.',
            'offer_id' => 'offer1,offer2',
            'show_icon' => '1',
            'sort_order' => 2,
        ], $overrides);
    }

    public function test_page_lists_every_registered_method_with_only_activate_buttons(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.payment-options.index'))
            ->assertOk()
            ->assertSeeInOrder(['Manual Payments', 'Bank Transfer / Deposit', 'Cash On Delivery (COD)', 'Payments Gateway', 'Razorpay Payment Gateway', 'Phonepe', 'Stripe Hosted', 'Instamojo Payment Gateway'])
            ->assertSee('images/payments/gateways/razorpay.svg', false)
            ->assertSee('images/payments/cards/cardemi.svg', false)
            ->assertSee('data-required-message="Please enter merchant name"', false)
            ->assertSee('value="activate"', false)
            ->assertDontSee('value="update"', false)
            ->assertDontSee('value="deactivate"', false);
    }

    public function test_activate_creates_the_payment_method(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.payment-options.save', 'cod'), [
                'payment_method' => 'cod',
                'intent' => 'activate',
                'instructions' => 'Only COD purchase.',
                'min_order_amount' => '500',
                'max_order_amount' => '1000',
                'extra_charge' => '100',
                'sort_order' => 1,
            ])
            ->assertRedirect(route('admin.payment-options.index').'#payment-method-cod')
            ->assertSessionHas('payment_status', ['code' => 'cod', 'message' => 'Cash On Delivery (COD) activated.']);

        $cod = PaymentMethod::firstWhere('code', 'cod');

        $this->assertTrue($cod->is_active);
        $this->assertNotNull($cod->activated_at);
        $this->assertSame('Only COD purchase.', $cod->instructions);
        $this->assertSame('500.00', $cod->min_order_amount);
        $this->assertSame(1, $cod->sort_order);
        $this->assertSame(['extra_charge' => '100'], $cod->settings);
    }

    public function test_activate_validates_required_fields_into_the_methods_error_bag(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.payment-options.save', 'razorpay'), $this->razorpayPayload([
                'key' => '',
                'secret_key' => '',
                'mode' => 'sandbox',
                'display_options' => ['jcb'],
                'min_order_amount' => '500',
                'max_order_amount' => '100',
            ]))
            ->assertSessionHasErrorsIn('razorpay', [
                'key' => 'Please enter key.',
                'secret_key' => 'Please enter secret key.',
                'mode',
                'display_options.0',
                'max_order_amount',
            ]);

        $this->assertSame(0, PaymentMethod::count());
    }

    public function test_update_changes_settings_and_keeps_a_blank_secret(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.payment-options.save', 'razorpay'), $this->razorpayPayload());

        $this->actingAs($admin, 'admin')
            ->post(route('admin.payment-options.save', 'razorpay'), $this->razorpayPayload([
                'intent' => 'update',
                'key' => 'rzp_live_key',
                'mode' => 'live',
                'secret_key' => '',
                'display_options' => [],
                'show_icon' => '0',
            ]))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('payment_status', ['code' => 'razorpay', 'message' => 'Razorpay Payment Gateway updated.']);

        $razorpay = PaymentMethod::firstWhere('code', 'razorpay');

        $this->assertSame('rzp_live_key', $razorpay->fieldValue('key'));
        $this->assertSame('live', $razorpay->fieldValue('mode'));
        $this->assertSame('rzp-secret-value', $razorpay->fieldValue('secret_key'));
        $this->assertSame([], $razorpay->display_options);
        $this->assertFalse($razorpay->show_icon);
        $this->assertStringNotContainsString('rzp-secret-value', DB::table('payment_methods')->value('settings'));

        $this->actingAs($admin, 'admin')
            ->get(route('admin.payment-options.index'))
            ->assertSee('value="update"', false)
            ->assertSee('value="deactivate"', false)
            ->assertSee('value="rzp_live_key"', false)
            ->assertSee('Saved — leave blank to keep')
            ->assertDontSee('rzp-secret-value');
    }

    public function test_deactivate_keeps_settings_and_shows_activate_again(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.payment-options.save', 'razorpay'), $this->razorpayPayload());

        $this->actingAs($admin, 'admin')
            ->post(route('admin.payment-options.save', 'razorpay'), ['intent' => 'deactivate'])
            ->assertSessionHas('payment_status', ['code' => 'razorpay', 'message' => 'Razorpay Payment Gateway deactivated.']);

        $razorpay = PaymentMethod::firstWhere('code', 'razorpay');
        $this->assertFalse($razorpay->is_active);
        $this->assertSame('rzp_test_key', $razorpay->fieldValue('key'));

        $this->actingAs($admin, 'admin')
            ->post(route('admin.payment-options.save', 'razorpay'), $this->razorpayPayload(['secret_key' => '']))
            ->assertSessionHasNoErrors();

        $this->assertTrue($razorpay->fresh()->is_active);
        $this->assertSame('rzp-secret-value', $razorpay->fresh()->fieldValue('secret_key'));
    }

    public function test_unknown_method_and_deactivating_a_never_activated_method_return_not_found(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.payment-options.save', 'paypal'), ['intent' => 'activate'])
            ->assertNotFound();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.payment-options.save', 'cod'), ['intent' => 'deactivate'])
            ->assertNotFound();
    }

    public function test_saving_requires_the_edit_permission(): void
    {
        $viewer = $this->admin(['payment-options.view']);

        $this->actingAs($viewer, 'admin')
            ->get(route('admin.payment-options.index'))
            ->assertOk();

        $this->actingAs($viewer, 'admin')
            ->post(route('admin.payment-options.save', 'cod'), ['intent' => 'activate', 'instructions' => 'COD'])
            ->assertForbidden();

        $this->assertSame(0, PaymentMethod::count());
    }

    public function test_phonepe_saves_android_and_ios_app_ids(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.payment-options.save', 'phonepe'), [
                'payment_method' => 'phonepe',
                'intent' => 'activate',
                'merchant_id' => 'PGTESTPAYUAT',
                'mode' => 'test',
                'key' => 'phonepe-key',
                'key_index' => '1',
                'android_app_id' => 'com.shambhavi.app',
                'ios_app_id' => 'id1234567890',
            ])
            ->assertSessionHasNoErrors();

        $phonepe = PaymentMethod::firstWhere('code', 'phonepe');
        $this->assertSame('com.shambhavi.app', $phonepe->fieldValue('android_app_id'));
        $this->assertSame('id1234567890', $phonepe->fieldValue('ios_app_id'));

        $this->actingAs($admin, 'admin')
            ->post(route('admin.payment-options.save', 'phonepe'), ['payment_method' => 'phonepe', 'intent' => 'update', 'mode' => 'test'])
            ->assertSessionHasErrorsIn('phonepe', [
                'android_app_id' => 'Please enter android app ID.',
                'ios_app_id' => 'Please enter iOS app ID.',
            ]);
    }
}
