<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VariantType;
use Illuminate\Support\Str;

class VariantTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $variantTypes = [
            [
                'variant_type_name' => 'Size',
                'description' => 'Product sizes such as S, M, L, XL and XXL.',
                'status' => 707,
                'sort_order' => 1,
            ],
            [
                'variant_type_name' => 'Color',
                'description' => 'Available product colors.',
                'status' => 707,
                'sort_order' => 2,
            ],
            [
                'variant_type_name' => 'Fabric',
                'description' => 'Fabric materials such as Cotton, Silk and Rayon.',
                'status' => 707,
                'sort_order' => 3,
            ],
            [
                'variant_type_name' => 'Pattern',
                'description' => 'Product patterns such as Printed, Plain and Embroidered.',
                'status' => 707,
                'sort_order' => 4,
            ],
            [
                'variant_type_name' => 'Sleeve Type',
                'description' => 'Sleeve styles for ready-made garments.',
                'status' => 707,
                'sort_order' => 5,
            ],
            [
                'variant_type_name' => 'Fit',
                'description' => 'Garment fitting options.',
                'status' => 707,
                'sort_order' => 6,
            ],
            [
                'variant_type_name' => 'Length',
                'description' => 'Available garment lengths.',
                'status' => 707,
                'sort_order' => 7,
            ],
        ];

        foreach ($variantTypes as $type) {
            $baseSlug = Str::slug($type['variant_type_name']);
            VariantType::updateOrCreate(
                ['variant_type_name' => $type['variant_type_name']],
                [
                    'slug' => $baseSlug,
                    'description' => $type['description'],
                    'status' => $type['status'],
                    'sort_order' => $type['sort_order'],
                ]
            );
        }
    }
}
