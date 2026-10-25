<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\PostalCode;
use RuntimeException;

class PostalCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jaipur = City::where('city_code', 'JAI')->first();
        if (!$jaipur) {
            throw new RuntimeException(
                'Jaipur not found. Run CitySeeder first.'
            );
        }

        $postalCodes = [
            [
                'postal_code' => '302001',
                'post_office_name' => 'Jaipur GPO',
                'office_type' => 'HO',
            ],
            [
                'postal_code' => '302004',
                'post_office_name' => 'Jawahar Nagar',
                'office_type' => 'SO',
            ],
            [
                'postal_code' => '302012',
                'post_office_name' => 'Shastri Nagar',
                'office_type' => 'SO',
            ],
            [
                'postal_code' => '302017',
                'post_office_name' => 'Malviya Nagar',
                'office_type' => 'SO',
            ],
            [
                'postal_code' => '302019',
                'post_office_name' => 'Mansarovar',
                'office_type' => 'SO',
            ],
            [
                'postal_code' => '302020',
                'post_office_name' => 'Mansarovar Sector',
                'office_type' => 'SO',
            ],
            [
                'postal_code' => '302021',
                'post_office_name' => 'Vaishali Nagar',
                'office_type' => 'SO',
            ],
        ];

        foreach ($postalCodes as $postalCode) {
            PostalCode::updateOrCreate(
                [
                    'city_id' => $jaipur->id,
                    'postal_code' => $postalCode['postal_code'],
                    'post_office_name' => $postalCode['post_office_name'],
                ],
                [
                    'office_type' => $postalCode['office_type'],
                    'delivery_status' => PostalCode::DELIVERY_AVAILABLE,
                    'status' => PostalCode::STATUS_ENABLED,
                ]
            );
        }
    }
}
