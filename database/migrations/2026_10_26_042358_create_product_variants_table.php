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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            // Sorted "variant_type_id:variant_value_id|..." pairs, unique per product
            $table->string('combination_key', 255);
            $table->string('sku', 100)->nullable()->unique();
            $table->string('barcode', 50)->nullable();
            $table->decimal('product_price', 10, 2);
            $table->decimal('discount_price', 10, 2)->default(0);
            $table->decimal('net_price', 10, 2);
            $table->decimal('weight', 8, 3)->nullable();        // kg, null = product weight
            $table->unsignedInteger('quantity')->nullable();
            $table->boolean('is_main')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['product_id', 'combination_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
