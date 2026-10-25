<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\State;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $india = Country::where('country_code', 'IN')->first();
        if (!$india) {
            throw new RuntimeException(
                'India not found in countries table. Run CountrySeeder first.'
            );
        }

        $states = [
            ['Andhra Pradesh', 'AP', '37', 'STATE', 'Amaravati'],
            ['Arunachal Pradesh', 'AR', '12', 'STATE', 'Itanagar'],
            ['Assam', 'AS', '18', 'STATE', 'Dispur'],
            ['Bihar', 'BR', '10', 'STATE', 'Patna'],
            ['Chhattisgarh', 'CG', '22', 'STATE', 'Raipur'],
            ['Goa', 'GA', '30', 'STATE', 'Panaji'],
            ['Gujarat', 'GJ', '24', 'STATE', 'Gandhinagar'],
            ['Haryana', 'HR', '06', 'STATE', 'Chandigarh'],
            ['Himachal Pradesh', 'HP', '02', 'STATE', 'Shimla'],
            ['Jharkhand', 'JH', '20', 'STATE', 'Ranchi'],
            ['Karnataka', 'KA', '29', 'STATE', 'Bengaluru'],
            ['Kerala', 'KL', '32', 'STATE', 'Thiruvananthapuram'],
            ['Madhya Pradesh', 'MP', '23', 'STATE', 'Bhopal'],
            ['Maharashtra', 'MH', '27', 'STATE', 'Mumbai'],
            ['Manipur', 'MN', '14', 'STATE', 'Imphal'],
            ['Meghalaya', 'ML', '17', 'STATE', 'Shillong'],
            ['Mizoram', 'MZ', '15', 'STATE', 'Aizawl'],
            ['Nagaland', 'NL', '13', 'STATE', 'Kohima'],
            ['Odisha', 'OD', '21', 'STATE', 'Bhubaneswar'],
            ['Punjab', 'PB', '03', 'STATE', 'Chandigarh'],
            ['Rajasthan', 'RJ', '08', 'STATE', 'Jaipur'],
            ['Sikkim', 'SK', '11', 'STATE', 'Gangtok'],
            ['Tamil Nadu', 'TN', '33', 'STATE', 'Chennai'],
            ['Telangana', 'TS', '36', 'STATE', 'Hyderabad'],
            ['Tripura', 'TR', '16', 'STATE', 'Agartala'],
            ['Uttar Pradesh', 'UP', '09', 'STATE', 'Lucknow'],
            ['Uttarakhand', 'UK', '05', 'STATE', 'Dehradun'],
            ['West Bengal', 'WB', '19', 'STATE', 'Kolkata'],
            // 8 Union Territories
            ['Andaman and Nicobar Islands', 'AN', '35', 'UT', 'Sri Vijaya Puram'],
            ['Chandigarh', 'CH', '04', 'UT', 'Chandigarh'],
            ['Dadra and Nagar Haveli and Daman and Diu', 'DH', '26', 'UT', 'Daman'],
            ['Delhi', 'DL', '07', 'UT', 'New Delhi'],
            ['Jammu and Kashmir', 'JK', '01', 'UT', 'Srinagar / Jammu'],
            ['Ladakh', 'LA', '38', 'UT', 'Leh'],
            ['Lakshadweep', 'LD', '31', 'UT', 'Kavaratti'],
            ['Puducherry', 'PY', '34', 'UT', 'Puducherry'],
        ];

        DB::transaction(function () use ($states, $india) {
            foreach ($states as $index => $state) {
                State::updateOrCreate(
                    ['state_code' => $state[1]],
                    [
                        'country_id' => $india->id,
                        'state_name' => $state[0],
                        'gst_state_code' => $state[2],
                        'state_type' => $state[3],
                        'capital' => $state[4],
                        'status' => State::STATUS_ENABLED,
                        'sort_order' => $index + 1,
                    ]
                );
            }
        });
    }
}
