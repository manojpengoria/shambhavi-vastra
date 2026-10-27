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
        Schema::create('size_charts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();              // e.g. Women Kurti
            $table->enum('unit', ['inch', 'cm'])->default('inch');
            // Table headers, e.g. ["Size", "Bust", "Waist", "Length"]
            $table->json('columns');
            // One array of cells per row, e.g. [["S", "34", "28", "44"], ...]
            $table->json('rows');
            $table->text('how_to_measure')->nullable();
            $table->string('image')->nullable();                // optional measuring guide image
            // 707 = Active, 505 = Inactive
            $table->unsignedSmallInteger('status')->default(707);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['status', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('size_charts');
    }
};
