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
        Schema::create('variant_types', function (Blueprint $table) {
            $table->id();
            $table->string('variant_type_name', 100)->unique();
            $table->string('slug', 120)->unique();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('status')
                ->default(707)
                ->comment('707 = Active, 505 = Inactive');
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
        Schema::dropIfExists('variant_types');
    }
};
