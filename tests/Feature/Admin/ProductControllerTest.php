<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Category;
use App\Models\VariantType;
use App\Models\VariantValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->assertSee('name="product_images[]"', false)
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
            ->assertSee('name="google_product_category"', false)
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
}
