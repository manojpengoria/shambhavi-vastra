<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\City;
use App\Models\State;
use Illuminate\Database\Seeder;
use RuntimeException;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rajasthan = State::where('state_code', 'RJ')->first();
        if (!$rajasthan) {
            throw new RuntimeException(
                'Rajasthan not found. Run StateSeeder first.'
            );
        }

        // Jaipur
        City::updateOrCreate(
            ['city_code' => 'JAI'],
            [
                'state_id' => $rajasthan->id,
                'city_name' => 'Jaipur',
                'city_type' => City::TYPE_CITY,
                'latitude' => 26.9124,
                'longitude' => 75.7873,
                'status' => City::STATUS_ENABLED,
                'sort_order' => 1,
            ]
        );

        // Jodhpur
        City::updateOrCreate(
            ['city_code' => 'JOD'],
            [
                'state_id' => $rajasthan->id,
                'city_name' => 'Jodhpur',
                'city_type' => City::TYPE_CITY,
                'latitude' => 26.2389,
                'longitude' => 73.0243,
                'status' => City::STATUS_ENABLED,
                'sort_order' => 2,
            ]
        );

        // Udaipur

        City::updateOrCreate(

            ['city_code' => 'UDP'],

            [

                'state_id' => $rajasthan->id,

                'city_name' => 'Udaipur',

                'city_type' => City::TYPE_CITY,

                'latitude' => 24.5854,

                'longitude' => 73.7125,

                'status' => City::STATUS_ENABLED,

                'sort_order' => 3,

            ]

        );

        // Kota

        City::updateOrCreate(

            ['city_code' => 'KOT'],

            [

                'state_id' => $rajasthan->id,

                'city_name' => 'Kota',

                'city_type' => City::TYPE_CITY,

                'latitude' => 25.2138,

                'longitude' => 75.8648,

                'status' => City::STATUS_ENABLED,

                'sort_order' => 4,

            ]

        );

        // Ajmer

        City::updateOrCreate(

            ['city_code' => 'AJM'],

            [

                'state_id' => $rajasthan->id,

                'city_name' => 'Ajmer',

                'city_type' => City::TYPE_CITY,

                'latitude' => 26.4499,

                'longitude' => 74.6399,

                'status' => City::STATUS_ENABLED,

                'sort_order' => 5,

            ]

        );

        // Bikaner

        City::updateOrCreate(

            ['city_code' => 'BIK'],

            [

                'state_id' => $rajasthan->id,

                'city_name' => 'Bikaner',

                'city_type' => City::TYPE_CITY,

                'latitude' => 28.0229,

                'longitude' => 73.3119,

                'status' => City::STATUS_ENABLED,

                'sort_order' => 6,

            ]

        );

        // Alwar

        City::updateOrCreate(

            ['city_code' => 'ALW'],

            [

                'state_id' => $rajasthan->id,

                'city_name' => 'Alwar',

                'city_type' => City::TYPE_CITY,

                'latitude' => 27.5530,

                'longitude' => 76.6346,

                'status' => City::STATUS_ENABLED,

                'sort_order' => 7,

            ]

        );

        // Bharatpur

        City::updateOrCreate(

            ['city_code' => 'BHR'],

            [

                'state_id' => $rajasthan->id,

                'city_name' => 'Bharatpur',

                'city_type' => City::TYPE_CITY,

                'latitude' => 27.2152,

                'longitude' => 77.5030,

                'status' => City::STATUS_ENABLED,

                'sort_order' => 8,

            ]

        );

        // Sikar

        City::updateOrCreate(

            ['city_code' => 'SIK'],

            [

                'state_id' => $rajasthan->id,

                'city_name' => 'Sikar',

                'city_type' => City::TYPE_CITY,

                'latitude' => 27.6094,

                'longitude' => 75.1398,

                'status' => City::STATUS_ENABLED,

                'sort_order' => 9,

            ]

        );

        // Pali

        City::updateOrCreate(

            ['city_code' => 'PAL'],

            [

                'state_id' => $rajasthan->id,

                'city_name' => 'Pali',

                'city_type' => City::TYPE_CITY,

                'latitude' => 25.7711,

                'longitude' => 73.3234,

                'status' => City::STATUS_ENABLED,

                'sort_order' => 10,

            ]

        );

        // Chittorgarh

        City::updateOrCreate(

            ['city_code' => 'CHT'],

            [

                'state_id' => $rajasthan->id,

                'city_name' => 'Chittorgarh',

                'city_type' => City::TYPE_CITY,

                'latitude' => 24.8887,

                'longitude' => 74.6269,

                'status' => City::STATUS_ENABLED,

                'sort_order' => 11,

            ]

        );

        // Barmer

        City::updateOrCreate(

            ['city_code' => 'BAR'],

            [

                'state_id' => $rajasthan->id,

                'city_name' => 'Barmer',

                'city_type' => City::TYPE_CITY,

                'latitude' => 25.7532,

                'longitude' => 71.4181,

                'status' => City::STATUS_ENABLED,

                'sort_order' => 12,

            ]

        );

        // Jaisalmer

        City::updateOrCreate(

            ['city_code' => 'JSM'],

            [

                'state_id' => $rajasthan->id,

                'city_name' => 'Jaisalmer',

                'city_type' => City::TYPE_CITY,

                'latitude' => 26.9157,

                'longitude' => 70.9083,

                'status' => City::STATUS_ENABLED,

                'sort_order' => 13,

            ]

        );

        // Jhunjhunu

        City::updateOrCreate(

            ['city_code' => 'JHJ'],

            [

                'state_id' => $rajasthan->id,

                'city_name' => 'Jhunjhunu',

                'city_type' => City::TYPE_CITY,

                'latitude' => 28.1289,

                'longitude' => 75.3995,

                'status' => City::STATUS_ENABLED,

                'sort_order' => 14,

            ]

        );

        // Dausa

        City::updateOrCreate(

            ['city_code' => 'DAU'],

            [

                'state_id' => $rajasthan->id,

                'city_name' => 'Dausa',

                'city_type' => City::TYPE_CITY,

                'latitude' => 26.8932,

                'longitude' => 76.3375,

                'status' => City::STATUS_ENABLED,

                'sort_order' => 15,

            ]

        );

        // Tonk

        City::updateOrCreate(

            ['city_code' => 'TON'],

            [

                'state_id' => $rajasthan->id,

                'city_name' => 'Tonk',

                'city_type' => City::TYPE_CITY,

                'latitude' => 26.1667,

                'longitude' => 75.7885,

                'status' => City::STATUS_ENABLED,

                'sort_order' => 16,

            ]

        );

        // Bundi

        City::updateOrCreate(

            ['city_code' => 'BUN'],

            [

                'state_id' => $rajasthan->id,

                'city_name' => 'Bundi',

                'city_type' => City::TYPE_CITY,

                'latitude' => 25.4305,

                'longitude' => 75.6499,

                'status' => City::STATUS_ENABLED,

                'sort_order' => 17,

            ]

        );

        // Sawai Madhopur

        City::updateOrCreate(

            ['city_code' => 'SWM'],

            [

                'state_id' => $rajasthan->id,

                'city_name' => 'Sawai Madhopur',

                'city_type' => City::TYPE_CITY,

                'latitude' => 26.0173,

                'longitude' => 76.5026,

                'status' => City::STATUS_ENABLED,

                'sort_order' => 18,

            ]

        );

        // Dholpur

        City::updateOrCreate(

            ['city_code' => 'DHO'],

            [

                'state_id' => $rajasthan->id,

                'city_name' => 'Dholpur',

                'city_type' => City::TYPE_CITY,

                'latitude' => 26.7025,

                'longitude' => 77.8934,

                'status' => City::STATUS_ENABLED,

                'sort_order' => 19,

            ]

        );

        // Nagaur

        City::updateOrCreate(

            ['city_code' => 'NAG'],

            [

                'state_id' => $rajasthan->id,

                'city_name' => 'Nagaur',

                'city_type' => City::TYPE_CITY,

                'latitude' => 27.2020,

                'longitude' => 73.7339,

                'status' => City::STATUS_ENABLED,

                'sort_order' => 20,

            ]

        );

        // Sri Ganganagar

        City::updateOrCreate(

            ['city_code' => 'SGN'],

            [

                'state_id' => $rajasthan->id,

                'city_name' => 'Sri Ganganagar',

                'city_type' => City::TYPE_CITY,

                'latitude' => 29.9038,

                'longitude' => 73.8772,

                'status' => City::STATUS_ENABLED,

                'sort_order' => 21,

            ]

        );

        // Hanumangarh

        City::updateOrCreate(

            ['city_code' => 'HNM'],

            [

                'state_id' => $rajasthan->id,

                'city_name' => 'Hanumangarh',

                'city_type' => City::TYPE_CITY,

                'latitude' => 29.5815,

                'longitude' => 74.3294,

                'status' => City::STATUS_ENABLED,

                'sort_order' => 22,

            ]

        );

        // Banswara

        City::updateOrCreate(

            ['city_code' => 'BNS'],

            [

                'state_id' => $rajasthan->id,

                'city_name' => 'Banswara',

                'city_type' => City::TYPE_CITY,

                'latitude' => 23.5461,

                'longitude' => 74.4349,

                'status' => City::STATUS_ENABLED,

                'sort_order' => 23,

            ]

        );

        // Dungarpur

        City::updateOrCreate(

            ['city_code' => 'DGR'],

            [

                'state_id' => $rajasthan->id,

                'city_name' => 'Dungarpur',

                'city_type' => City::TYPE_CITY,

                'latitude' => 23.8431,

                'longitude' => 73.7147,

                'status' => City::STATUS_ENABLED,

                'sort_order' => 24,

            ]

        );

        // Rajsamand

        City::updateOrCreate(

            ['city_code' => 'RJS'],

            [

                'state_id' => $rajasthan->id,

                'city_name' => 'Rajsamand',

                'city_type' => City::TYPE_CITY,

                'latitude' => 25.0715,

                'longitude' => 73.8798,

                'status' => City::STATUS_ENABLED,

                'sort_order' => 25,

            ]

        );

        // Pratapgarh

        City::updateOrCreate(

            ['city_code' => 'PRT'],

            [

                'state_id' => $rajasthan->id,

                'city_name' => 'Pratapgarh',

                'city_type' => City::TYPE_CITY,

                'latitude' => 24.0320,

                'longitude' => 74.7780,

                'status' => City::STATUS_ENABLED,

                'sort_order' => 26,

            ]

        );

        // Jhalawar

        City::updateOrCreate(

            ['city_code' => 'JHW'],

            [

                'state_id' => $rajasthan->id,

                'city_name' => 'Jhalawar',

                'city_type' => City::TYPE_CITY,

                'latitude' => 24.5968,

                'longitude' => 76.1649,

                'status' => City::STATUS_ENABLED,

                'sort_order' => 27,

            ]

        );

        // Sirohi

        City::updateOrCreate(

            ['city_code' => 'SIH'],

            [

                'state_id' => $rajasthan->id,

                'city_name' => 'Sirohi',

                'city_type' => City::TYPE_CITY,

                'latitude' => 24.8850,

                'longitude' => 72.8575,

                'status' => City::STATUS_ENABLED,

                'sort_order' => 28,

            ]

        );
    }
}
