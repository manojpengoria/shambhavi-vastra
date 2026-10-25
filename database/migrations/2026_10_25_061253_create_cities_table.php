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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('state_id')
                ->constrained('states')
                ->restrictOnDelete();
            $table->string('city_name', 150);
            // Internal unique city code
            $table->string('city_code', 20)->unique();
            // CITY, TOWN, VILLAGE
            $table->string('city_type', 20)->default('CITY');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            // 707 = Enabled, 505 = Disabled
            $table->unsignedSmallInteger('status')->default(707);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['state_id', 'status']);
            $table->index('city_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
