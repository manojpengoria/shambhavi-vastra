<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The original products table only had id + timestamps, so it is rebuilt
     * with the full schema. It refuses to run if any product already exists.
     */
    public function up(): void
    {
        if (Schema::hasTable('products') && DB::table('products')->exists()) {
            throw new RuntimeException('The products table is not empty; add the columns with an alter migration instead.');
        }

        Schema::dropIfExists('products');

        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Details
            $table->string('product_name', 255);
            $table->string('sku', 100)->unique();
            $table->string('barcode', 50)->nullable();
            $table->string('hsn_sac_code', 8)->nullable();
            $table->string('short_summary_brand', 255)->nullable();
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->string('origin_country', 100)->nullable();
            $table->string('youtube_video', 20)->nullable();

            // Pricing
            $table->decimal('selling_price', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('net_price', 10, 2);
            $table->decimal('purchase_cost', 10, 2)->nullable();
            $table->unsignedInteger('moq')->default(1);
            $table->boolean('taxable')->default(false);
            $table->decimal('tax_rate', 5, 2)->nullable();

            // Inventory
            $table->boolean('track_inventory')->default(false);
            $table->unsignedInteger('stock_quantity')->nullable();
            $table->boolean('has_variants')->default(false);

            // Shipping
            $table->decimal('weight', 8, 3);                    // kg, including packaging
            $table->decimal('length', 8, 2)->nullable();        // cm
            $table->decimal('breadth', 8, 2)->nullable();       // cm
            $table->decimal('height', 8, 2)->nullable();        // cm
            $table->foreignId('shipping_class_id')->nullable()->constrained('shipping_classes')->nullOnDelete();
            $table->foreignId('shipping_package_id')->nullable()->constrained('shipping_packages')->nullOnDelete();
            $table->boolean('free_shipping')->default(false);
            $table->boolean('cod_available')->default(true);
            $table->unsignedSmallInteger('shipping_days')->nullable();
            $table->unsignedSmallInteger('out_of_stock_days')->nullable();
            $table->boolean('is_returnable')->default(true);
            $table->unsignedSmallInteger('return_days')->nullable(); // null = store default

            // Labels & SEO
            $table->string('label', 20)->nullable();            // popular, new, custom
            $table->string('custom_label', 50)->nullable();
            $table->string('seo_url', 255)->unique();
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();

            // Visibility
            // 707 = Visible, 505 = Hidden
            $table->unsignedSmallInteger('status')->default(707);
            $table->boolean('is_draft')->default(true);
            $table->timestamp('publish_at')->nullable();
            $table->boolean('visible_after_login')->default(false);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['status', 'is_draft']);
            $table->index('product_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }
};
