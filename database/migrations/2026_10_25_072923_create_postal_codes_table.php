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
        Schema::create('postal_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')
                ->constrained('cities')
                ->restrictOnDelete();
            // Indian PIN Code
            $table->string('postal_code', 10);
            // Post office information
            $table->string('post_office_name', 150)->nullable();
            // HO, SO, BO, etc.
            $table->string('office_type', 30)->nullable();
            // 707 = Delivery Available, 505 = Unavailable
            $table->unsignedSmallInteger('delivery_status')->default(707);
            // 707 = Enabled, 505 = Disabled
            $table->unsignedSmallInteger('status')->default(707);
            $table->timestamps();
            $table->unique(
                ['city_id', 'postal_code', 'post_office_name'],
                'postal_codes_city_code_office_unique'
            );
            $table->index(['postal_code', 'status']);
            $table->index(['city_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postal_codes');
    }
};
