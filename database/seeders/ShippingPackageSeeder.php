<?php

namespace Database\Seeders;

use App\Models\ShippingPackage;
use Illuminate\Database\Seeder;

class ShippingPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            ['code' => 'PKG-POUCH', 'name' => 'Small pouch', 'length' => 30, 'breadth' => 25, 'height' => 3,  'box_weight' => 0.03, 'max_weight' => 1,  'cost' => 6],
            ['code' => 'PKG-SAREE', 'name' => 'Saree box',   'length' => 38, 'breadth' => 28, 'height' => 6,  'box_weight' => 0.12, 'max_weight' => 2,  'cost' => 15],
            ['code' => 'PKG-MED',   'name' => 'Medium box',  'length' => 35, 'breadth' => 28, 'height' => 8,  'box_weight' => 0.15, 'max_weight' => 3,  'cost' => 18],
            ['code' => 'PKG-LRG',   'name' => 'Large box',   'length' => 45, 'breadth' => 35, 'height' => 12, 'box_weight' => 0.30, 'max_weight' => 6,  'cost' => 30],
        ];

        foreach ($packages as $package) {
            ShippingPackage::updateOrCreate(['code' => $package['code']], $package + ['is_active' => true]);
        }
    }
}
