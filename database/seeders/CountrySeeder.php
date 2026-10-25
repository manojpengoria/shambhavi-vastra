<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['country_name' => 'Afghanistan', 'country_code' => 'AF', 'dial_code' => '+93'],
            ['country_name' => 'Albania', 'country_code' => 'AL', 'dial_code' => '+355'],
            ['country_name' => 'Algeria', 'country_code' => 'DZ', 'dial_code' => '+213'],
            ['country_name' => 'Andorra', 'country_code' => 'AD', 'dial_code' => '+376'],
            ['country_name' => 'Angola', 'country_code' => 'AO', 'dial_code' => '+244'],
            ['country_name' => 'Argentina', 'country_code' => 'AR', 'dial_code' => '+54'],
            ['country_name' => 'Armenia', 'country_code' => 'AM', 'dial_code' => '+374'],
            ['country_name' => 'Australia', 'country_code' => 'AU', 'dial_code' => '+61'],
            ['country_name' => 'Austria', 'country_code' => 'AT', 'dial_code' => '+43'],
            ['country_name' => 'Azerbaijan', 'country_code' => 'AZ', 'dial_code' => '+994'],
            ['country_name' => 'Bahrain', 'country_code' => 'BH', 'dial_code' => '+973'],
            ['country_name' => 'Bangladesh', 'country_code' => 'BD', 'dial_code' => '+880'],
            ['country_name' => 'Belgium', 'country_code' => 'BE', 'dial_code' => '+32'],
            ['country_name' => 'Bhutan', 'country_code' => 'BT', 'dial_code' => '+975'],
            ['country_name' => 'Brazil', 'country_code' => 'BR', 'dial_code' => '+55'],
            ['country_name' => 'Canada', 'country_code' => 'CA', 'dial_code' => '+1'],
            ['country_name' => 'China', 'country_code' => 'CN', 'dial_code' => '+86'],
            ['country_name' => 'Egypt', 'country_code' => 'EG', 'dial_code' => '+20'],
            ['country_name' => 'France', 'country_code' => 'FR', 'dial_code' => '+33'],
            ['country_name' => 'Germany', 'country_code' => 'DE', 'dial_code' => '+49'],
            ['country_name' => 'India', 'country_code' => 'IN', 'dial_code' => '+91'],
            ['country_name' => 'Indonesia', 'country_code' => 'ID', 'dial_code' => '+62'],
            ['country_name' => 'Iran', 'country_code' => 'IR', 'dial_code' => '+98'],
            ['country_name' => 'Iraq', 'country_code' => 'IQ', 'dial_code' => '+964'],
            ['country_name' => 'Ireland', 'country_code' => 'IE', 'dial_code' => '+353'],
            ['country_name' => 'Israel', 'country_code' => 'IL', 'dial_code' => '+972'],
            ['country_name' => 'Italy', 'country_code' => 'IT', 'dial_code' => '+39'],
            ['country_name' => 'Japan', 'country_code' => 'JP', 'dial_code' => '+81'],
            ['country_name' => 'Kenya', 'country_code' => 'KE', 'dial_code' => '+254'],
            ['country_name' => 'Kuwait', 'country_code' => 'KW', 'dial_code' => '+965'],
            ['country_name' => 'Malaysia', 'country_code' => 'MY', 'dial_code' => '+60'],
            ['country_name' => 'Maldives', 'country_code' => 'MV', 'dial_code' => '+960'],
            ['country_name' => 'Mexico', 'country_code' => 'MX', 'dial_code' => '+52'],
            ['country_name' => 'Nepal', 'country_code' => 'NP', 'dial_code' => '+977'],
            ['country_name' => 'Netherlands', 'country_code' => 'NL', 'dial_code' => '+31'],
            ['country_name' => 'New Zealand', 'country_code' => 'NZ', 'dial_code' => '+64'],
            ['country_name' => 'Nigeria', 'country_code' => 'NG', 'dial_code' => '+234'],
            ['country_name' => 'Norway', 'country_code' => 'NO', 'dial_code' => '+47'],
            ['country_name' => 'Oman', 'country_code' => 'OM', 'dial_code' => '+968'],
            ['country_name' => 'Pakistan', 'country_code' => 'PK', 'dial_code' => '+92'],
            ['country_name' => 'Philippines', 'country_code' => 'PH', 'dial_code' => '+63'],
            ['country_name' => 'Poland', 'country_code' => 'PL', 'dial_code' => '+48'],
            ['country_name' => 'Portugal', 'country_code' => 'PT', 'dial_code' => '+351'],
            ['country_name' => 'Qatar', 'country_code' => 'QA', 'dial_code' => '+974'],
            ['country_name' => 'Russia', 'country_code' => 'RU', 'dial_code' => '+7'],
            ['country_name' => 'Saudi Arabia', 'country_code' => 'SA', 'dial_code' => '+966'],
            ['country_name' => 'Singapore', 'country_code' => 'SG', 'dial_code' => '+65'],
            ['country_name' => 'South Africa', 'country_code' => 'ZA', 'dial_code' => '+27'],
            ['country_name' => 'South Korea', 'country_code' => 'KR', 'dial_code' => '+82'],
            ['country_name' => 'Spain', 'country_code' => 'ES', 'dial_code' => '+34'],
            ['country_name' => 'Sri Lanka', 'country_code' => 'LK', 'dial_code' => '+94'],
            ['country_name' => 'Sweden', 'country_code' => 'SE', 'dial_code' => '+46'],
            ['country_name' => 'Switzerland', 'country_code' => 'CH', 'dial_code' => '+41'],
            ['country_name' => 'Thailand', 'country_code' => 'TH', 'dial_code' => '+66'],
            ['country_name' => 'Turkey', 'country_code' => 'TR', 'dial_code' => '+90'],
            ['country_name' => 'Ukraine', 'country_code' => 'UA', 'dial_code' => '+380'],
            ['country_name' => 'United Arab Emirates', 'country_code' => 'AE', 'dial_code' => '+971'],
            ['country_name' => 'United Kingdom', 'country_code' => 'GB', 'dial_code' => '+44'],
            ['country_name' => 'United States', 'country_code' => 'US', 'dial_code' => '+1'],
            ['country_name' => 'Vietnam', 'country_code' => 'VN', 'dial_code' => '+84'],
        ];

        DB::transaction(function () use ($countries) {
            foreach ($countries as $country) {
                Country::updateOrCreate(
                    ['country_code' => $country['country_code']],
                    [
                        'country_name' => $country['country_name'],
                        'dial_code' => $country['dial_code'],
                        'status' => Country::STATUS_ENABLED,
                    ]
                );
            }
        });
    }
}
