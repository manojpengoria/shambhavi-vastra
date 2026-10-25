<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Single-row table: hamesha id = 1 wala record use hoga
        Schema::create('shipping_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('default_warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->foreignId('default_courier_partner_id')->nullable()->constrained('courier_partners')->nullOnDelete();
            $table->unsignedInteger('volumetric_divisor')->default(5000);
            $table->decimal('free_shipping_above', 10, 2)->nullable();
            $table->boolean('cod_enabled')->default(true);
            $table->decimal('cod_min_order', 10, 2)->nullable();
            $table->decimal('cod_max_order', 10, 2)->nullable();
            $table->unsignedSmallInteger('return_window_days')->default(7);
            $table->enum('return_shipping_paid_by', ['customer', 'seller'])->default('customer');
            $table->boolean('show_delivery_date')->default(true);
            $table->boolean('pincode_check_before_cart')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_settings');
    }
};
