<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        // Location masters se postalCodes dhoondhna (apne column naam ke hisaab se badal lein)
        $postalCodes = DB::table('postal_codes')->where('postal_code', '302021')->first();

        if (! $postalCodes) {
            if ($this->command) {
                $this->command->warn('Pincode 302021 location master me nahi mila, warehouse skip kiya.');
            }
            return;
        }

        $stateId = DB::table('cities')->where('id', $postalCodes->city_id)->value('state_id');
        $areaId = DB::table('areas')->where('postal_code_id', $postalCodes->id)->value('id');

        Warehouse::updateOrCreate(
            ['code' => 'WH-JPR'],
            [
                'name'             => 'Main godown',
                'contact_person'   => 'Manoj Gupta',
                'mobile'           => '9999999999',      // asli number daalein
                'email'            => null,
                'address_line'     => 'Shop 12, Vaishali nagar',
                'state_id'         => $stateId,
                'city_id'          => $postalCodes->city_id,
                'postal_code_id'   => $postalCodes->id,
                'area_id'          => $areaId,
                'gstin'            => null,              // asli GSTIN daalein
                'pickup_time_slot' => '10 AM - 2 PM',
                'is_default'       => true,
                'is_active'        => true,
            ]
        );
    }
}
