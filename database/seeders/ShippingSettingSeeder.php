<?php

namespace Database\Seeders;

use App\Models\CourierPartner;
use App\Models\ShippingSetting;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class ShippingSettingSeeder extends Seeder
{
    public function run(): void
    {
        ShippingSetting::updateOrCreate(
            ['id' => 1],
            [
                'default_warehouse_id'       => Warehouse::where('is_default', true)->value('id'),
                'default_courier_partner_id' => CourierPartner::where('code', 'DLV')->value('id'),
                'volumetric_divisor'         => 5000,
                'free_shipping_above'        => 999,
                'cod_enabled'                => true,
                'cod_min_order'              => 299,
                'cod_max_order'              => 15000,
                'return_window_days'         => 7,
                'return_shipping_paid_by'    => ShippingSetting::PAID_BY_CUSTOMER,
                'show_delivery_date'         => true,
                'pincode_check_before_cart'  => true,
            ]
        );
    }
}
