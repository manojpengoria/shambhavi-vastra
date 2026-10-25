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
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')
                ->constrained('countries')
                ->restrictOnDelete();
            $table->string('state_name', 100);
            // ISO 3166-2 subdivision code, e.g. IN-RJ
            $table->string('state_code', 10)->unique();
            // Indian GST state code, e.g. Rajasthan = 08
            $table->string('gst_state_code', 2)->nullable()->unique();
            // STATE or UT
            $table->enum('state_type', ['STATE', 'UT'])
                ->default('STATE');
            $table->string('capital', 100)->nullable();
            // 707 = Enabled, 505 = Disabled
            $table->unsignedSmallInteger('status')->default(707);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['country_id', 'status']);
            $table->index(['state_type', 'status']);
            $table->index('state_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
