<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * One row per payment method the admin has activated at least once.
     * Available methods and their fields are defined in config/payment_methods.php.
     */
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            // Key from config/payment_methods.php, e.g. cod, razorpay
            $table->string('code', 50)->unique();
            $table->boolean('is_active')->default(true);

            // Settings shared by every method (used at checkout)
            $table->text('instructions')->nullable();
            $table->decimal('min_order_amount', 10, 2)->nullable();
            $table->decimal('max_order_amount', 10, 2)->nullable();
            $table->json('display_options')->nullable();       // card/brand codes shown at footer & checkout
            $table->boolean('show_icon')->default(false);
            $table->unsignedInteger('sort_order')->default(0);

            // Method specific settings (API keys, merchant id, mode, COD charges...), encrypted
            $table->text('settings')->nullable();

            $table->timestamp('activated_at')->nullable();       // first activation
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
