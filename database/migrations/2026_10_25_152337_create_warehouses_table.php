<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 20)->unique();             // WH-JPR
            $table->string('contact_person', 100);
            $table->string('mobile', 15);
            $table->string('email', 150)->nullable();
            $table->string('address_line', 255);
            // Location masters se link (pehle se bane hue tables)
            $table->foreignId('state_id')->constrained('states')->restrictOnDelete();
            $table->foreignId('city_id')->constrained('cities')->restrictOnDelete();
            $table->foreignId('postal_code_id')->constrained('postal_codes')->restrictOnDelete();
            $table->foreignId('area_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->string('gstin', 15)->nullable();
            $table->string('pickup_time_slot', 50)->nullable(); // "10 AM - 2 PM"
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
