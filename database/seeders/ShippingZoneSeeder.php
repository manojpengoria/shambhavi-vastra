<?php

namespace Database\Seeders;

use App\Models\ShippingZone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShippingZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            [
                'code' => 'Z-LOCAL', 'name' => 'Local Jaipur', 'coverage_type' => 'city', 'priority' => 1,
                'description' => 'Jaipur city', 'cities' => ['Jaipur'],
            ],
            [
                'code' => 'Z-RJ', 'name' => 'Rest of Rajasthan', 'coverage_type' => 'state', 'priority' => 2,
                'description' => 'Rajasthan except Jaipur', 'states' => ['Rajasthan'],
            ],
            [
                'code' => 'Z-METRO', 'name' => 'Metro', 'coverage_type' => 'city', 'priority' => 3,
                'description' => 'Bade metro shehar',
                'cities' => ['Delhi', 'New Delhi', 'Mumbai', 'Kolkata', 'Chennai', 'Bengaluru', 'Hyderabad', 'Pune', 'Ahmedabad'],
            ],
            [
                'code' => 'Z-NEJK', 'name' => 'North-East & J&K', 'coverage_type' => 'state', 'priority' => 4,
                'description' => 'Door-daraz / special zone',
                'states' => ['Arunachal Pradesh', 'Assam', 'Manipur', 'Meghalaya', 'Mizoram', 'Nagaland',
                             'Sikkim', 'Tripura', 'Jammu and Kashmir', 'Ladakh', 'Andaman and Nicobar Islands', 'Lakshadweep'],
            ],
            [
                'code' => 'Z-ROI', 'name' => 'Rest of India', 'coverage_type' => 'all', 'priority' => 9,
                'description' => 'Baaki pura India',
            ],
        ];

        foreach ($zones as $data) {
            $zone = ShippingZone::updateOrCreate(
                ['code' => $data['code']],
                [
                    'name'          => $data['name'],
                    'coverage_type' => $data['coverage_type'],
                    'priority'      => $data['priority'],
                    'description'   => $data['description'],
                    'is_active'     => true,
                ]
            );

            if (! empty($data['states'])) {
                $zone->states()->sync($this->idsByName('states', $data['states']));
            }

            if (! empty($data['cities'])) {
                $zone->cities()->sync($this->idsByName('cities', $data['cities']));
            }
        }
    }

    /** Naam se id nikalna; jo naam master me na mile uski warning */
    private function idsByName(string $table, array $names): array
    {
        $lower = array_map('mb_strtolower', $names);

        if (! empty($table) && $table === 'cities') {
            $rows = DB::table($table)
                ->whereIn(DB::raw('LOWER(city_name)'), $lower)
                ->pluck('id', 'city_name');
        }
        if (! empty($table) && $table === 'states') {
            $rows = DB::table($table)
                ->whereIn(DB::raw('LOWER(state_name)'), $lower)
                ->pluck('id', 'state_name');
        }

        $found   = array_map('mb_strtolower', $rows->keys()->all());
        $missing = array_diff($lower, $found);

        if ($missing) {
            $this->command?->warn("{$table} me nahi mile: " . implode(', ', $missing));
        }

        return $rows->values()->all();
    }
}
