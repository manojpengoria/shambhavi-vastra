<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Chalane ke liye:  php artisan db:seed --class=ShippingDatabaseSeeder
 * Location masters (states, cities, pincodes, areas) pehle se bhare hone chahiye.
 */
class ShippingDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            WarehouseSeeder::class,
            // CourierPartnerSeeder::class,
            // ShippingMethodSeeder::class,
            // ShippingZoneSeeder::class,
            // ShippingPackageSeeder::class,
            // ShippingClassSeeder::class,
            // ShippingSettingSeeder::class,   // sabse aakhir me, default IDs ke liye
        ]);
    }
}
