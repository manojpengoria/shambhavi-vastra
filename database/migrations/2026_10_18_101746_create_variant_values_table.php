<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('variant_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_type_id')
                ->constrained('variant_types')
                ->cascadeOnDelete();
            $table->string('variant_value_name', 100);
            $table->string('slug', 120);
            $table->string('color_code', 7)->nullable();
            $table->unsignedSmallInteger('status')
                ->default(707)
                ->comment('707 = Active, 505 = Inactive');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(
                ['variant_type_id', 'variant_value_name'],
                'variant_values_type_value_unique'
            );
            $table->unique(
                ['variant_type_id', 'slug'],
                'variant_values_type_slug_unique'
            );
            $table->index(['variant_type_id', 'status', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variant_values');
    }
};
