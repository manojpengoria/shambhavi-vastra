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
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')
                ->constrained('cities')
                ->restrictOnDelete();
            $table->foreignId('postal_code_id')
                ->nullable()
                ->constrained('postal_codes')
                ->nullOnDelete();
            $table->string('area_name', 150);
            $table->string('area_code', 30)->unique();
            // LOCALITY, COLONY, SECTOR, VILLAGE
            $table->string('area_type', 30)->default('LOCALITY');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            // 707 = Delivery Available, 505 = Unavailable
            $table->unsignedSmallInteger('delivery_available')
                ->default(707);
            // 707 = Enabled, 505 = Disabled
            $table->unsignedSmallInteger('status')->default(707);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['city_id', 'status']);
            $table->index(['postal_code_id', 'status']);
            $table->index(['area_name', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('areas');
    }
};
