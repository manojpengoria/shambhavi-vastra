<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Product specifications from the Attribute master (attributes not used for
     * variants), e.g. Fabric: Cotton, Occasion: Festive.
     */
    public function up(): void
    {
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('variant_type_id')->constrained('variant_types')->restrictOnDelete();
            $table->foreignId('variant_value_id')->constrained('variant_values')->restrictOnDelete();
            $table->unique(['product_id', 'variant_value_id']);
            $table->index(['variant_type_id', 'variant_value_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attribute_values');
    }
};
