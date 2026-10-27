<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Product;
use App\Models\SizeChart;
use App\Models\VariantType;
use App\Models\VariantValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AttributeAndSizeChartTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<int, string>  $permissions
     */
    private function admin(array $permissions = ['size-charts.view', 'size-charts.create', 'size-charts.edit', 'size-charts.delete']): Admin
    {
        $admin = Admin::firstOrCreate(['email' => 'catalog-admin@example.com'], [
            'name' => 'Catalog Admin',
            'password' => 'password',
            'is_active' => true,
        ]);

        foreach ($permissions as $permission) {
            $admin->givePermissionTo(Permission::findOrCreate($permission, 'admin'));
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $admin;
    }

    private function product(): Product
    {
        return Product::create([
            'product_name' => 'Cotton Kurti',
            'sku' => 'CK-1',
            'seo_url' => 'cotton-kurti',
            'selling_price' => 999,
            'net_price' => 999,
            'weight' => 0.4,
        ]);
    }

    public function test_attribute_is_created_with_the_used_for_variants_flag(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.variant-type.store'), [
                'variant_type_name' => 'Fabric',
                'used_for_variants' => '0',
                'status' => 707,
                'sort_order' => 3,
            ])
            ->assertRedirect(route('admin.variant-type.index'))
            ->assertSessionHas('success', 'Attribute created successfully.');

        $this->assertFalse(VariantType::firstWhere('slug', 'fabric')->used_for_variants);

        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.variant-type.index'))
            ->assertOk()
            ->assertSee('Attributes')
            ->assertSee('Used for variants');
    }

    public function test_used_for_variants_cannot_change_while_products_use_the_attribute(): void
    {
        $admin = $this->admin();
        $color = VariantType::create(['variant_type_name' => 'Color', 'slug' => 'color', 'used_for_variants' => true, 'status' => 707, 'sort_order' => 1]);
        $red = VariantValue::create(['variant_type_id' => $color->id, 'variant_value_name' => 'Red', 'slug' => 'red', 'status' => 707, 'sort_order' => 1]);
        $variant = $this->product()->variants()->create(['combination_key' => "{$color->id}:{$red->id}", 'product_price' => 999, 'net_price' => 999]);
        $variant->values()->sync([$red->id => ['variant_type_id' => $color->id]]);

        $payload = ['variant_type_name' => 'Color', 'used_for_variants' => '0', 'status' => 707, 'sort_order' => 1];

        $this->actingAs($admin, 'admin')
            ->put(route('admin.variant-type.update', $color), $payload)
            ->assertSessionHasErrors('used_for_variants');

        $this->assertTrue($color->fresh()->used_for_variants);

        $this->actingAs($admin, 'admin')
            ->delete(route('admin.variant-values.destroy', $red))
            ->assertSessionHas('error');

        $this->assertModelExists($red);

        $variant->values()->detach();

        $this->actingAs($admin, 'admin')
            ->put(route('admin.variant-type.update', $color), $payload)
            ->assertSessionHasNoErrors();

        $this->assertFalse($color->fresh()->used_for_variants);
    }

    public function test_size_chart_is_created_with_trimmed_rows_and_image(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.size-charts.store'), [
                'name' => 'Women Kurti',
                'unit' => 'inch',
                'columns' => [' Size ', 'Bust', 'Waist'],
                'rows' => [
                    ['S', '34', '28'],
                    ['M', '36', ''],
                    ['', '', ''],
                ],
                'how_to_measure' => 'Measure around the fullest part.',
                'image' => UploadedFile::fake()->image('guide.png'),
                'status' => 707,
                'sort_order' => 1,
            ])
            ->assertRedirect(route('admin.size-charts.index'))
            ->assertSessionHasNoErrors();

        $sizeChart = SizeChart::firstOrFail();

        $this->assertSame(['Size', 'Bust', 'Waist'], $sizeChart->columns);
        $this->assertSame([['S', '34', '28'], ['M', '36', null]], $sizeChart->rows);
        Storage::disk('public')->assertExists($sizeChart->image);

        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.size-charts.index'))
            ->assertOk()
            ->assertSee('Women Kurti')
            ->assertSee('S, M');
    }

    public function test_size_chart_validation(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.size-charts.store'), [
                'name' => '',
                'unit' => 'feet',
                'columns' => ['Size', 'size'],
                'rows' => [['', '34'], ['M', '36'], ['M', '38']],
                'status' => 707,
                'sort_order' => 0,
            ])
            ->assertSessionHasErrors(['name', 'unit', 'columns.1', 'rows.0.0', 'rows.2.0']);

        $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.size-charts.store'), [
                'name' => 'Empty',
                'unit' => 'cm',
                'columns' => ['Size', 'Bust'],
                'rows' => [['S', ''], ['M', '']],
                'status' => 707,
                'sort_order' => 0,
            ])
            ->assertSessionHasErrors('rows');

        $this->assertSame(0, SizeChart::count());
    }

    public function test_size_chart_in_use_cannot_be_deleted_and_edit_works(): void
    {
        $admin = $this->admin();
        $sizeChart = SizeChart::create(['name' => 'Men Shirt', 'unit' => 'inch', 'columns' => ['Size', 'Chest'], 'rows' => [['M', '40']]]);
        $product = $this->product();
        $product->update(['size_chart_id' => $sizeChart->id]);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.size-charts.edit', $sizeChart))
            ->assertOk()
            ->assertSee('value="Chest"', false)
            ->assertSee('name="rows[0][1]"', false);

        $this->actingAs($admin, 'admin')
            ->delete(route('admin.size-charts.destroy', $sizeChart))
            ->assertSessionHas('error');

        $this->assertModelExists($sizeChart);

        $product->update(['size_chart_id' => null]);

        $this->actingAs($admin, 'admin')
            ->delete(route('admin.size-charts.destroy', $sizeChart))
            ->assertSessionHas('success');

        $this->assertModelMissing($sizeChart);
    }

    public function test_size_chart_pages_require_permission(): void
    {
        $viewer = $this->admin(['size-charts.view']);

        $this->actingAs($viewer, 'admin')->get(route('admin.size-charts.index'))->assertOk();
        $this->actingAs($viewer, 'admin')->get(route('admin.size-charts.create'))->assertForbidden();
    }

    public function test_size_chart_create_form_prefills_sizes_from_the_size_attribute(): void
    {
        $size = VariantType::create(['variant_type_name' => 'Size', 'slug' => 'size', 'used_for_variants' => true, 'status' => 707, 'sort_order' => 1]);
        foreach (['S', 'M', 'L'] as $index => $name) {
            VariantValue::create(['variant_type_id' => $size->id, 'variant_value_name' => $name, 'slug' => strtolower($name), 'status' => 707, 'sort_order' => $index]);
        }

        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.size-charts.create'))
            ->assertOk()
            ->assertSeeInOrder(['name="rows[0][0]"', 'value="S"', 'name="rows[1][0]"', 'value="M"', 'name="rows[2][0]"', 'value="L"'], false);
    }
}
