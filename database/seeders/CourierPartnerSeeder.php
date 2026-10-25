<?php

namespace Database\Seeders;

use App\Models\CourierPartner;
use Illuminate\Database\Seeder;

class CourierPartnerSeeder extends Seeder
{
    public function run(): void
    {
        // Tracking URL format apne courier account/panel se confirm kar lein
        $couriers = [
            [
                'code' => 'DLV', 'name' => 'Delhivery', 'integration_type' => 'api',
                'tracking_url' => 'https://www.delhivery.com/track/package/{AWB}',
                'priority' => 1, 'max_weight' => 20, 'max_cod_amount' => 50000,
                'supports_cod' => true, 'supports_reverse_pickup' => true, 'supports_express' => true,
            ],
            [
                'code' => 'SRK', 'name' => 'Shiprocket', 'integration_type' => 'api',
                'tracking_url' => 'https://shiprocket.co/tracking/{AWB}',
                'priority' => 2, 'max_weight' => 20, 'max_cod_amount' => 50000,
                'supports_cod' => true, 'supports_reverse_pickup' => true, 'supports_express' => true,
            ],
            [
                'code' => 'IPOST', 'name' => 'India Post', 'integration_type' => 'manual',
                'tracking_url' => null,
                'priority' => 3, 'max_weight' => 20, 'max_cod_amount' => 10000,
                'supports_cod' => true, 'supports_reverse_pickup' => false, 'supports_express' => false,
            ],
            [
                'code' => 'SELF', 'name' => 'Self delivery (Jaipur)', 'integration_type' => 'manual',
                'tracking_url' => null,
                'priority' => 9, 'max_weight' => null, 'max_cod_amount' => null,
                'supports_cod' => true, 'supports_reverse_pickup' => true, 'supports_express' => true,
            ],
        ];

        foreach ($couriers as $courier) {
            CourierPartner::updateOrCreate(
                ['code' => $courier['code']],
                $courier + ['is_active' => true]   // API key admin panel se bharein
            );
        }
    }
}
