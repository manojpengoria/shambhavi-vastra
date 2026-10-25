<?php

namespace Database\Seeders;

use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class ShippingMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['code' => 'STD', 'name' => 'Standard', 'customer_text' => '4-7 din me delivery', 'display_order' => 1, 'is_active' => true],
            ['code' => 'EXP', 'name' => 'Express', 'customer_text' => '1-3 din me fast delivery', 'display_order' => 2, 'is_active' => true],
            ['code' => 'SDJ', 'name' => 'Same day (Jaipur)', 'customer_text' => 'Aaj hi delivery', 'display_order' => 3, 'is_active' => false],
        ];

        foreach ($methods as $method) {
            ShippingMethod::updateOrCreate(['code' => $method['code']], $method);
        }
    }
}
