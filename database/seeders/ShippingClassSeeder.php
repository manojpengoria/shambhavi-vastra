<?php

namespace Database\Seeders;

use App\Models\ShippingClass;
use Illuminate\Database\Seeder;

class ShippingClassSeeder extends Seeder
{
    public function run(): void
    {
        $classes = [
            ['code' => 'LIGHT', 'name' => 'Light', 'extra_charge' => 0,   'description' => 'Saree, kurti, dupatta'],
            ['code' => 'HEAVY', 'name' => 'Heavy', 'extra_charge' => 60,  'description' => 'Lehenga sets, bridal wear'],
            ['code' => 'BULKY', 'name' => 'Bulky', 'extra_charge' => 100, 'description' => 'Wholesale bundles, bade parcel'],
        ];

        foreach ($classes as $class) {
            ShippingClass::updateOrCreate(['code' => $class['code']], $class + ['is_active' => true]);
        }
    }
}
