<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 20)->unique();
            // state = chuni hui states, city = chuni hui cities, all = baaki pura India
            $table->enum('coverage_type', ['state', 'city', 'all'])->default('state');
            $table->unsignedSmallInteger('priority')->default(1); // chhota number jeetega
            $table->string('description', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Zone -> States (coverage_type = state)
        Schema::create('shipping_zone_state', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_zone_id')->constrained('shipping_zones')->cascadeOnDelete();
            $table->foreignId('state_id')->constrained('states')->restrictOnDelete();
            $table->unique(['shipping_zone_id', 'state_id']);
        });

        // Zone -> Cities (coverage_type = city)
        Schema::create('shipping_zone_city', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_zone_id')->constrained('shipping_zones')->cascadeOnDelete();
            $table->foreignId('city_id')->constrained('cities')->restrictOnDelete();
            $table->unique(['shipping_zone_id', 'city_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_zone_city');
        Schema::dropIfExists('shipping_zone_state');
        Schema::dropIfExists('shipping_zones');
    }
};
