<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courier_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 20)->unique();              // DLV
            $table->string('logo')->nullable();                // storage path
            $table->enum('integration_type', ['api', 'manual'])->default('manual');
            $table->text('api_key')->nullable();               // encrypted cast (model)
            $table->text('api_secret')->nullable();            // encrypted cast (model)
            $table->string('tracking_url')->nullable();        // https://.../{AWB}
            $table->unsignedSmallInteger('priority')->default(1); // 1 = sabse pehle
            $table->decimal('max_weight', 8, 3)->nullable();   // kg
            $table->decimal('max_cod_amount', 10, 2)->nullable();
            $table->boolean('supports_cod')->default(true);
            $table->boolean('supports_reverse_pickup')->default(false);
            $table->boolean('supports_express')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_partners');
    }
};
