<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourierPartner;
use App\Models\ShippingSetting;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ShippingSettingController extends Controller
{
    /**
     * Show the shipping settings form.
     */
    public function edit(): View
    {
        $shippingSetting = ShippingSetting::query()->firstOrCreate(['id' => 1]);

        $warehouses = Warehouse::query()
            ->where(fn ($query) => $query->active()->orWhere('id', $shippingSetting->default_warehouse_id))
            ->orderBy('name')
            ->get();

        $courierPartners = CourierPartner::query()
            ->where(fn ($query) => $query->where('is_active', true)->orWhere('id', $shippingSetting->default_courier_partner_id))
            ->orderBy('priority')
            ->orderBy('name')
            ->get();

        return view('admin.shipping-settings.edit', compact('shippingSetting', 'warehouses', 'courierPartners'));
    }

    /**
     * Update the shipping settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $shippingSetting = ShippingSetting::query()->firstOrCreate(['id' => 1]);

        $validated = $request->validate([
            'default_warehouse_id' => [
                'nullable',
                'integer',
                Rule::exists('warehouses', 'id')->where('is_active', true),
            ],
            'default_courier_partner_id' => [
                'nullable',
                'integer',
                Rule::exists('courier_partners', 'id')->where('is_active', true),
            ],
            'volumetric_divisor' => ['required', 'integer', 'min:1', 'max:100000'],
            'free_shipping_above' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'cod_enabled' => ['required', 'boolean'],
            'cod_min_order' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'cod_max_order' => [
                'nullable',
                'numeric',
                'min:0',
                'max:99999999.99',
                Rule::when($request->filled('cod_min_order'), 'gte:cod_min_order'),
            ],
            'return_window_days' => ['required', 'integer', 'min:0', 'max:365'],
            'return_shipping_paid_by' => ['required', Rule::in(array_keys(ShippingSetting::RETURN_PAID_BY))],
            'show_delivery_date' => ['required', 'boolean'],
            'pincode_check_before_cart' => ['required', 'boolean'],
        ], [
            'cod_max_order.gte' => 'The COD maximum order must be greater than or equal to the COD minimum order.',
        ]);

        DB::transaction(function () use ($shippingSetting, $validated) {
            $shippingSetting->update($validated);

            // Mirror the default warehouse on the warehouses table (the model unsets the previous default).
            if ($validated['default_warehouse_id']) {
                Warehouse::findOrFail($validated['default_warehouse_id'])->update(['is_default' => true]);
            } else {
                Warehouse::where('is_default', true)->update(['is_default' => false]);
            }
        });

        return redirect()
            ->route('admin.shipping-settings.edit')
            ->with('success', 'Shipping settings updated successfully.');
    }
}
