<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Country;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ShippingClass;
use App\Models\ShippingPackage;
use App\Models\VariantType;
use App\Models\VariantValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_form_shows_searchable_active_categories_in_hierarchy_order(): void
    {
        $createProductsPermission = Permission::create([
            'name' => 'products.create',
            'guard_name' => 'admin',
        ]);
        $admin = Admin::create([
            'name' => 'Product Manager',
            'email' => 'product-manager@example.com',
            'password' => 'password',
            'is_active' => true,
        ]);
        $admin->givePermissionTo($createProductsPermission);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $sarees = Category::create([
            'category_name' => 'Sarees',
            'seo_url' => 'product-test-sarees',
            'status' => 707,
            'sort_order' => 1,
        ]);
        $byFabric = Category::create([
            'parent_id' => $sarees->id,
            'category_name' => 'By Fabric',
            'seo_url' => 'product-test-by-fabric',
            'status' => 707,
            'sort_order' => 1,
        ]);
        Category::create([
            'category_name' => 'Inactive Category',
            'seo_url' => 'product-test-inactive',
            'status' => 505,
            'sort_order' => 2,
        ]);
        $colorType = VariantType::create([
            'variant_type_name' => 'Color',
            'slug' => 'product-test-color',
            'status' => 707,
            'sort_order' => 1,
        ]);
        VariantValue::create([
            'variant_type_id' => $colorType->id,
            'variant_value_name' => 'Red',
            'slug' => 'product-test-red',
            'status' => 707,
            'sort_order' => 1,
        ]);
        VariantValue::create([
            'variant_type_id' => $colorType->id,
            'variant_value_name' => 'Inactive Color',
            'slug' => 'product-test-inactive-color',
            'status' => 505,
            'sort_order' => 2,
        ]);
        VariantType::create([
            'variant_type_name' => 'Inactive Type',
            'slug' => 'product-test-inactive-type',
            'status' => 505,
            'sort_order' => 2,
        ]);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.products.create'))
            ->assertOk()
            ->assertSee('id="select2_sample_modal_5"', false)
            ->assertSee('name="category_ids[]"', false)
            ->assertSee('multiple', false)
            ->assertSeeInOrder([
                'value="'.$sarees->id.'"',
                'Sarees',
                'value="'.$byFabric->id.'"',
                'Sarees / By Fabric',
            ], false)
            ->assertSee('name="product_name"', false)
            ->assertSee('id="summernote_1"', false)
            ->assertSee('id="seo_url"', false)
            ->assertSee('id="meta_title"', false)
            ->assertSee('id="meta_keywords"', false)
            ->assertSee('id="meta_description"', false)
            ->assertSee('id="seo-preview-url"', false)
            ->assertSee('id="seo-preview-description"', false)
            ->assertSee('name="sku"', false)
            ->assertSee('name="product_images[1]"', false)
            ->assertSee('data-image-slot', false)
            ->assertSee('class="product-image-preview"', false)
            ->assertSee('class="product-image-remove"', false)
            ->assertSee('name="main_product_image"', false)
            ->assertSeeInOrder([
                'id="main_product_image_1"',
                'value="1"',
                'checked',
            ], false)
            ->assertSee('name="selling_price"', false)
            ->assertSee('id="net_price"', false)
            ->assertSee('readonly', false)
            ->assertSee('id="tax_rate"', false)
            ->assertSee('id="checkbox31"', false)
            ->assertSee('name="variants[0][variant_type_id]"', false)
            ->assertSee('name="variants[0][variant_value_ids][]"', false)
            ->assertSee('Color', false)
            ->assertSee('Red', false)
            ->assertSee('id="generated-variants-section"', false)
            ->assertSee('Select which variants will be created:', false)
            ->assertSee('Product Price', false)
            ->assertSee('Discount Price', false)
            ->assertSee('Barcode (Optional)', false)
            ->assertSee('Main', false)
            ->assertSee('name="shipping_days"', false)
            ->assertSee('name="shipping_class_id"', false)
            ->assertSee('name="shipping_package_id"', false)
            ->assertSee('name="cod_available"', false)
            ->assertSee('name="is_returnable"', false)
            ->assertSee('id="toggle-publish-schedule"', false)
            ->assertSee('id="publish_at"', false)
            ->assertSee('type="datetime-local"', false)
            ->assertSee('data-on-text="YES"', false)
            ->assertSee('Visible After Customer Login', false)
            ->assertSee('Search Engines Friendly', false)
            ->assertSee('id="toggle-product-seo-details"', false)
            ->assertSee('Show Details', false)
            ->assertSee('id="has_variants"', false)
            ->assertSee('id="generate_variants"', false)
            ->assertSee('id="custom-label-field"', false)
            ->assertSee('id="custom_label"', false)
            ->assertDontSee('Inactive Category', false)
            ->assertDontSee('Inactive Type', false)
            ->assertDontSee('Inactive Color', false)
            ->assertDontSee('red, blue', false);
    }

    private function productManager(): Admin
    {
        $admin = Admin::create([
            'name' => 'Catalog Manager',
            'email' => 'catalog-manager@example.com',
            'password' => 'password',
            'is_active' => true,
        ]);

        foreach (['view', 'create', 'edit', 'delete'] as $ability) {
            $admin->givePermissionTo(Permission::findOrCreate("products.{$ability}", 'admin'));
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $admin;
    }

    private function category(): Category
    {
        return Category::create([
            'category_name' => 'Sarees',
            'seo_url' => 'sarees',
            'status' => 707,
            'sort_order' => 1,
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function productPayload(Category $category, array $overrides = []): array
    {
        return array_merge([
            'action' => 'publish',
            'category_ids' => [$category->id],
            'is_visible' => '1',
            'visible_after_login' => '0',
            'product_name' => 'Banarasi Silk Saree',
            'sort_order' => 1,
            'sku' => 'BSS-001',
            'hsn_sac_code' => '5007',
            'selling_price' => '2500',
            'discount' => '500',
            'moq' => 1,
            'taxable' => '1',
            'tax_rate' => '5',
            'track_inventory' => '1',
            'stock_quantity' => 10,
            'has_variants' => '0',
            'weight' => '0.8',
            'length' => '30',
            'breadth' => '25',
            'height' => '5',
            'free_shipping' => '0',
            'cod_available' => '1',
            'shipping_days' => 2,
            'out_of_stock_days' => 7,
            'is_returnable' => '1',
            'return_days' => 10,
            'label' => 'new',
            'product_images' => [
                1 => UploadedFile::fake()->image('front.jpg'),
                3 => UploadedFile::fake()->image('back.png'),
            ],
            'main_product_image' => 3,
        ], $overrides);
    }

    /**
     * @return array{0: VariantValue, 1: VariantValue, 2: VariantValue}
     */
    private function sizeAndColorValues(): array
    {
        $color = VariantType::create(['variant_type_name' => 'Color', 'slug' => 'color', 'status' => 707, 'sort_order' => 1]);
        $size = VariantType::create(['variant_type_name' => 'Size', 'slug' => 'size', 'status' => 707, 'sort_order' => 2]);

        return [
            VariantValue::create(['variant_type_id' => $color->id, 'variant_value_name' => 'Red', 'slug' => 'red', 'status' => 707, 'sort_order' => 1]),
            VariantValue::create(['variant_type_id' => $size->id, 'variant_value_name' => 'M', 'slug' => 'm', 'status' => 707, 'sort_order' => 1]),
            VariantValue::create(['variant_type_id' => $size->id, 'variant_value_name' => 'L', 'slug' => 'l', 'status' => 707, 'sort_order' => 2]),
        ];
    }

    private function key(VariantValue ...$values): string
    {
        return collect($values)
            ->sortBy('variant_type_id')
            ->map(fn (VariantValue $value) => "{$value->variant_type_id}:{$value->id}")
            ->implode('|');
    }

    public function test_simple_product_is_published_with_images_categories_and_shipping(): void
    {
        Storage::fake('public');
        $category = $this->category();
        Country::create(['country_name' => 'India', 'country_code' => 'IN', 'dial_code' => '+91']);
        $shippingClass = ShippingClass::create(['name' => 'Heavy', 'code' => 'HEAVY', 'extra_charge' => 50]);
        $shippingPackage = ShippingPackage::create(['name' => 'Medium box', 'code' => 'BOX-M', 'length' => 30, 'breadth' => 25, 'height' => 5]);

        $this->actingAs($this->productManager(), 'admin')
            ->post(route('admin.products.store'), $this->productPayload($category, [
                'origin_country' => 'India',
                'shipping_class_id' => $shippingClass->id,
                'shipping_package_id' => $shippingPackage->id,
            ]))
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHasNoErrors();

        $product = Product::firstOrFail();

        $this->assertSame('banarasi-silk-saree', $product->seo_url);
        $this->assertSame('2000.00', $product->net_price);
        $this->assertFalse($product->is_draft);
        $this->assertSame(Product::STATUS_VISIBLE, $product->status);
        $this->assertSame(10, $product->stock_quantity);
        $this->assertSame($shippingClass->id, $product->shipping_class_id);
        $this->assertSame(10, $product->return_days);
        $this->assertSame([$category->id], $product->categories()->pluck('categories.id')->all());
        $this->assertSame([1, 3], $product->images()->pluck('sort_order')->all());
        $this->assertSame(3, $product->mainImage->sort_order);
        $product->images->each(fn (ProductImage $image) => Storage::disk('public')->assertExists($image->image_path));
    }

    public function test_publishing_requires_an_image_but_draft_does_not(): void
    {
        Storage::fake('public');
        $category = $this->category();
        $admin = $this->productManager();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.products.store'), $this->productPayload($category, ['product_images' => []]))
            ->assertSessionHasErrors('product_images');

        $this->actingAs($admin, 'admin')
            ->post(route('admin.products.store'), $this->productPayload($category, ['product_images' => [], 'action' => 'draft']))
            ->assertSessionHasNoErrors();

        $this->assertTrue(Product::firstOrFail()->is_draft);
    }

    public function test_product_validation_rules(): void
    {
        Storage::fake('public');
        $category = $this->category();

        $this->actingAs($this->productManager(), 'admin')
            ->post(route('admin.products.store'), $this->productPayload($category, [
                'category_ids' => [],
                'discount' => '3000',
                'tax_rate' => '',
                'stock_quantity' => '',
                'weight' => '',
                'height' => '',
                'out_of_stock_days' => 1,
                'hsn_sac_code' => '12',
                'youtube_video' => 'not a video id',
                'label' => 'custom',
                'product_images' => [13 => UploadedFile::fake()->image('extra.jpg')],
            ]))
            ->assertSessionHasErrors([
                'category_ids',
                'discount',
                'tax_rate',
                'stock_quantity',
                'weight',
                'height',
                'out_of_stock_days',
                'hsn_sac_code',
                'youtube_video',
                'custom_label',
                'product_images',
            ]);

        $this->assertSame(0, Product::count());
    }

    public function test_product_with_variants_is_created_with_values_and_main_variant(): void
    {
        Storage::fake('public');
        $category = $this->category();
        [$red, $medium, $large] = $this->sizeAndColorValues();

        $this->actingAs($this->productManager(), 'admin')
            ->post(route('admin.products.store'), $this->productPayload($category, [
                'has_variants' => '1',
                'stock_quantity' => '',
                'product_variants' => [
                    ['enabled' => '1', 'key' => "{$medium->variant_type_id}:{$medium->id}|{$red->variant_type_id}:{$red->id}", 'product_price' => '2500', 'discount_price' => '200', 'sku' => 'BSS-RED-M', 'quantity' => 4],
                    ['enabled' => '1', 'key' => $this->key($red, $large), 'product_price' => '2700', 'discount_price' => '', 'sku' => 'BSS-RED-L', 'weight' => '0.9', 'quantity' => 2],
                    ['key' => $this->key($red), 'product_price' => '1'],
                ],
                'main_variant_key' => $this->key($red, $large),
            ]))
            ->assertSessionHasNoErrors();

        $product = Product::with('variants.values')->firstOrFail();

        $this->assertTrue($product->has_variants);
        $this->assertNull($product->stock_quantity);
        $this->assertCount(2, $product->variants);
        $this->assertSame($this->key($red, $medium), $product->variants[0]->combination_key);
        $this->assertSame('2300.00', $product->variants[0]->net_price);
        $this->assertSame('Red / M', $product->variants[0]->label());
        $this->assertTrue($product->variants[1]->is_main);
        $this->assertFalse($product->variants[0]->is_main);
    }

    public function test_variant_validation_rejects_duplicate_skus_invalid_values_and_missing_quantity(): void
    {
        Storage::fake('public');
        $category = $this->category();
        [$red, $medium, $large] = $this->sizeAndColorValues();
        $admin = $this->productManager();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.products.store'), $this->productPayload($category, [
                'has_variants' => '1',
                'product_variants' => [
                    ['enabled' => '1', 'key' => $this->key($red, $medium), 'product_price' => '100', 'discount_price' => '150', 'sku' => 'DUP'],
                    ['enabled' => '1', 'key' => $this->key($red, $large), 'product_price' => '100', 'sku' => 'dup'],
                ],
                'main_variant_key' => $this->key($red, $medium),
            ]))
            ->assertSessionHasErrors([
                'enabled_product_variants.0.discount_price',
                'enabled_product_variants.0.sku',
                'enabled_product_variants.0.quantity',
            ]);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.products.store'), $this->productPayload($category, [
                'has_variants' => '1',
                'track_inventory' => '0',
                'product_variants' => [
                    ['enabled' => '1', 'key' => "{$medium->variant_type_id}:{$red->id}", 'product_price' => '100'],
                ],
                'main_variant_key' => "{$medium->variant_type_id}:{$red->id}",
            ]))
            ->assertSessionHasErrors('product_variants');

        $this->actingAs($admin, 'admin')
            ->post(route('admin.products.store'), $this->productPayload($category, [
                'has_variants' => '1',
                'track_inventory' => '0',
            ]))
            ->assertSessionHasErrors('enabled_product_variants');

        $this->assertSame(0, Product::count());
    }

    public function test_edit_form_shows_saved_images_variants_and_shipping(): void
    {
        Storage::fake('public');
        $category = $this->category();
        [$red, $medium] = $this->sizeAndColorValues();
        $admin = $this->productManager();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.products.store'), $this->productPayload($category, [
                'has_variants' => '1',
                'track_inventory' => '0',
                'product_variants' => [
                    ['enabled' => '1', 'key' => $this->key($red, $medium), 'product_price' => '2500', 'sku' => 'BSS-RED-M'],
                ],
                'main_variant_key' => $this->key($red, $medium),
            ]))
            ->assertSessionHasNoErrors();

        $product = Product::firstOrFail();

        $this->actingAs($admin, 'admin')
            ->get(route('admin.products.edit', $product))
            ->assertOk()
            ->assertSee('value="Banarasi Silk Saree"', false)
            ->assertSee('name="existing_image_ids[1]"', false)
            ->assertSee('name="existing_image_ids[3]"', false)
            ->assertSee('BSS-RED-M', false)
            ->assertSee('data-main-variant-key="'.$this->key($red, $medium).'"', false)
            ->assertSee('value="0.800"', false);
    }

    public function test_update_keeps_retained_images_replaces_slots_and_syncs_variants(): void
    {
        Storage::fake('public');
        $category = $this->category();
        [$red, $medium, $large] = $this->sizeAndColorValues();
        $admin = $this->productManager();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.products.store'), $this->productPayload($category, [
                'has_variants' => '1',
                'track_inventory' => '0',
                'product_variants' => [
                    ['enabled' => '1', 'key' => $this->key($red, $medium), 'product_price' => '2500', 'sku' => 'SKU-A'],
                    ['enabled' => '1', 'key' => $this->key($red, $large), 'product_price' => '2600', 'sku' => 'SKU-B'],
                ],
                'main_variant_key' => $this->key($red, $medium),
            ]))
            ->assertSessionHasNoErrors();

        $product = Product::with(['images', 'variants'])->firstOrFail();
        $frontImage = $product->images->firstWhere('sort_order', 1);
        $backImage = $product->images->firstWhere('sort_order', 3);
        $mediumVariant = $product->variants->firstWhere('combination_key', $this->key($red, $medium));

        $this->actingAs($admin, 'admin')
            ->put(route('admin.products.update', $product), $this->productPayload($category, [
                'product_name' => 'Banarasi Silk Saree Deluxe',
                'custom_seo_url' => '1',
                'seo_url' => 'banarasi-silk-saree',
                'has_variants' => '1',
                'track_inventory' => '0',
                'existing_image_ids' => [1 => $frontImage->id],
                'product_images' => [2 => UploadedFile::fake()->image('side.webp')],
                'main_product_image' => 2,
                'product_variants' => [
                    // Swap SKU with the removed variant to check the unique constraint is freed first.
                    ['enabled' => '1', 'key' => $this->key($red, $medium), 'product_price' => '2400', 'sku' => 'SKU-B'],
                ],
                'main_variant_key' => $this->key($red, $medium),
            ]))
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHasNoErrors();

        $product->refresh()->load(['images', 'variants']);

        $this->assertSame('Banarasi Silk Saree Deluxe', $product->product_name);
        $this->assertSame('banarasi-silk-saree', $product->seo_url);
        $this->assertSame([1, 2], $product->images->pluck('sort_order')->all());
        $this->assertSame(2, $product->mainImage->sort_order);
        $this->assertModelMissing($backImage);
        Storage::disk('public')->assertMissing($backImage->image_path);
        Storage::disk('public')->assertExists($frontImage->image_path);
        $this->assertCount(1, $product->variants);
        $this->assertSame($mediumVariant->id, $product->variants[0]->id);
        $this->assertSame('SKU-B', $product->variants[0]->sku);
        $this->assertSame('2400.00', $product->variants[0]->product_price);
    }

    public function test_switching_off_variants_removes_them(): void
    {
        Storage::fake('public');
        $category = $this->category();
        [$red, $medium] = $this->sizeAndColorValues();
        $admin = $this->productManager();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.products.store'), $this->productPayload($category, [
                'has_variants' => '1',
                'track_inventory' => '0',
                'product_variants' => [
                    ['enabled' => '1', 'key' => $this->key($red, $medium), 'product_price' => '2500'],
                ],
                'main_variant_key' => $this->key($red, $medium),
            ]));

        $product = Product::firstOrFail();

        $this->actingAs($admin, 'admin')
            ->put(route('admin.products.update', $product), $this->productPayload($category, [
                'existing_image_ids' => $product->images()->pluck('id', 'sort_order')->all(),
                'product_images' => [],
            ]))
            ->assertSessionHasNoErrors();

        $this->assertFalse($product->fresh()->has_variants);
        $this->assertSame(0, $product->variants()->count());
        $this->assertSame(2, $product->images()->count());
    }

    public function test_product_is_deleted_with_images_and_variants(): void
    {
        Storage::fake('public');
        $category = $this->category();
        $admin = $this->productManager();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.products.store'), $this->productPayload($category));

        $product = Product::with('images')->firstOrFail();
        $imagePaths = $product->images->pluck('image_path');

        $this->actingAs($admin, 'admin')
            ->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products.index'));

        $this->assertModelMissing($product);
        $this->assertSame(0, ProductImage::count());
        $imagePaths->each(fn (string $path) => Storage::disk('public')->assertMissing($path));
    }

    public function test_index_lists_and_filters_products(): void
    {
        Storage::fake('public');
        $category = $this->category();
        $admin = $this->productManager();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.products.store'), $this->productPayload($category));
        $this->actingAs($admin, 'admin')
            ->post(route('admin.products.store'), $this->productPayload($category, [
                'action' => 'draft',
                'product_name' => 'Cotton Kurta',
                'sku' => 'CK-001',
                'product_images' => [],
            ]));

        $this->actingAs($admin, 'admin')
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertSee('Banarasi Silk Saree')
            ->assertSee('Cotton Kurta');

        $this->actingAs($admin, 'admin')
            ->get(route('admin.products.index', ['status' => 'draft']))
            ->assertOk()
            ->assertSee('Cotton Kurta')
            ->assertDontSee('BSS-001');
    }

    public function test_product_pages_require_permission(): void
    {
        $admin = Admin::create([
            'name' => 'Order Staff',
            'email' => 'order-staff@example.com',
            'password' => 'password',
            'is_active' => true,
        ]);

        $this->actingAs($admin, 'admin')->get(route('admin.products.index'))->assertForbidden();
        $this->actingAs($admin, 'admin')->post(route('admin.products.store'), [])->assertForbidden();
    }

    public function test_main_image_survives_saving_again_with_the_same_main_image(): void
    {
        Storage::fake('public');
        $category = $this->category();
        $admin = $this->productManager();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.products.store'), $this->productPayload($category))
            ->assertSessionHasNoErrors();

        $product = Product::firstOrFail();
        $mainImage = $product->mainImage;

        foreach (['draft', 'publish'] as $action) {
            $this->actingAs($admin, 'admin')
                ->put(route('admin.products.update', $product), $this->productPayload($category, [
                    'action' => $action,
                    'existing_image_ids' => $product->images()->pluck('id', 'sort_order')->all(),
                    'product_images' => [],
                    'main_product_image' => $mainImage->sort_order,
                ]))
                ->assertSessionHasNoErrors();

            $this->assertSame($mainImage->id, $product->mainImage()->value('id'));
            $this->assertSame(1, $product->images()->where('is_main', true)->count());
        }

        $this->actingAs($admin, 'admin')
            ->get(route('admin.products.index'))
            ->assertSee($mainImage->url(), false);
    }
}
