<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);                     // Medium box
            $table->string('code', 20)->unique();
            $table->decimal('length', 8, 2);                 // cm
            $table->decimal('breadth', 8, 2);                // cm
            $table->decimal('height', 8, 2);                 // cm
            $table->decimal('box_weight', 8, 3)->default(0); // kg, dabbe ka khud ka weight
            $table->decimal('max_weight', 8, 3)->nullable(); // kg
            $table->decimal('cost', 10, 2)->nullable();      // packaging cost
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_packages');
    }
};
