<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * variant_types is the Attribute master: Yes = used to build product variants
     * (Size, Color), No = product specification only (Fabric, Fit, Pattern...).
     */
    public function up(): void
    {
        Schema::table('variant_types', function (Blueprint $table) {
            $table->boolean('used_for_variants')->default(false)->after('description');
        });

        // Existing variant attributes: Size, Color and anything already used by a product variant.
        DB::table('variant_types')
            ->whereIn('slug', ['size', 'color', 'colour'])
            ->orWhereIn('id', DB::table('product_variant_values')->select('variant_type_id'))
            ->update(['used_for_variants' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('variant_types', function (Blueprint $table) {
            $table->dropColumn('used_for_variants');
        });
    }
};
