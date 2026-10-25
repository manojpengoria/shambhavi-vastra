<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Area;
use App\Models\City;
use App\Models\PostalCode;
use Illuminate\Database\Seeder;
use RuntimeException;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jaipur = City::where('city_code', 'JAI')->first();
        if (!$jaipur) {
            throw new RuntimeException(
                'Jaipur not found. Run RajasthanCitySeeder first.'
            );
        }

        $areas = [
            [
                'area_name' => 'Vaishali Nagar',
                'area_code' => 'JAI-VAI',
                'postal_code' => '302021',
                'area_type' => Area::TYPE_LOCALITY,
                'sort_order' => 1,
            ],
            [
                'area_name' => 'Malviya Nagar',
                'area_code' => 'JAI-MAL',
                'postal_code' => '302017',
                'area_type' => Area::TYPE_LOCALITY,
                'sort_order' => 2,
            ],
            [
                'area_name' => 'Mansarovar',
                'area_code' => 'JAI-MAN',
                'postal_code' => '302020',
                'area_type' => Area::TYPE_LOCALITY,
                'sort_order' => 3,
            ],
            [
                'area_name' => 'C-Scheme',
                'area_code' => 'JAI-CSC',
                'postal_code' => '302001',
                'area_type' => Area::TYPE_LOCALITY,
                'sort_order' => 4,
            ],
            [
                'area_name' => 'Shastri Nagar',
                'area_code' => 'JAI-SHN',
                'postal_code' => '302012',
                'area_type' => Area::TYPE_LOCALITY,
                'sort_order' => 5,
            ],
        ];

        foreach ($areas as $areaData) {
            $postalCode = PostalCode::where(
                'city_id',
                $jaipur->id
            )->where(
                'postal_code',
                $areaData['postal_code']
            )->first();

            Area::updateOrCreate(
                [
                    'area_code' => $areaData['area_code'],
                ],
                [
                    'city_id' => $jaipur->id,
                    'postal_code_id' => $postalCode?->id,
                    'area_name' => $areaData['area_name'],
                    'area_type' => $areaData['area_type'],
                    'latitude' => null,
                    'longitude' => null,
                    'delivery_available' => Area::DELIVERY_AVAILABLE,
                    'status' => Area::STATUS_ENABLED,
                    'sort_order' => $areaData['sort_order'],
                ]
            );
        }
    }
}
