<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_form_lists_categories_with_parent_paths_in_hierarchy_order(): void
    {
        $createCategoriesPermission = Permission::create([
            'name' => 'categories.create',
            'guard_name' => 'admin',
        ]);
        $admin = Admin::create([
            'name' => 'Category Manager',
            'email' => 'category-manager@example.com',
            'password' => 'password',
            'is_active' => true,
        ]);
        $admin->givePermissionTo($createCategoriesPermission);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $sarees = Category::create([
            'category_name' => 'Sarees',
            'seo_url' => 'sarees',
            'sort_order' => 1,
        ]);
        $byFabric = Category::create([
            'parent_id' => $sarees->id,
            'category_name' => 'By Fabric',
            'seo_url' => 'sarees-by-fabric',
            'sort_order' => 1,
        ]);
        $pureBanarasiSilk = Category::create([
            'parent_id' => $byFabric->id,
            'category_name' => 'Pure Banarasi Silk',
            'seo_url' => 'pure-banarasi-silk',
            'sort_order' => 1,
        ]);
        Category::create([
            'category_name' => 'Blouses',
            'seo_url' => 'blouses',
            'sort_order' => 2,
        ]);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.categories.create'))
            ->assertOk()
            ->assertSeeInOrder([
                'No Parent',
                'value="'.$sarees->id.'"',
                'Sarees',
                'value="'.$byFabric->id.'"',
                'Sarees / By Fabric',
                'value="'.$pureBanarasiSilk->id.'"',
                'Sarees / By Fabric / Pure Banarasi Silk',
                'Blouses',
            ], false);
    }

    public function test_edit_form_loads_existing_category_data_and_eligible_parents(): void
    {
        $editCategoriesPermission = Permission::create([
            'name' => 'categories.edit',
            'guard_name' => 'admin',
        ]);
        $admin = Admin::create([
            'name' => 'Category Editor',
            'email' => 'category-editor@example.com',
            'password' => 'password',
            'is_active' => true,
        ]);
        $admin->givePermissionTo($editCategoriesPermission);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $parent = Category::create([
            'category_name' => 'Sarees',
            'seo_url' => 'sarees-edit-test',
            'status' => 707,
            'sort_order' => 1,
        ]);
        $category = Category::create([
            'parent_id' => $parent->id,
            'category_name' => 'By Fabric',
            'seo_url' => 'sarees-by-fabric-edit-test',
            'meta_title' => 'Sarees by Fabric',
            'meta_keywords' => 'silk, cotton',
            'meta_description' => 'Browse sarees by fabric.',
            'summary' => 'Fabric-based saree collection.',
            'status' => 505,
            'sort_order' => 2,
        ]);
        Category::create([
            'parent_id' => $category->id,
            'category_name' => 'Pure Silk',
            'seo_url' => 'pure-silk-edit-test',
            'status' => 707,
            'sort_order' => 1,
        ]);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.categories.edit', $category))
            ->assertOk()
            ->assertSee(route('admin.categories.update', $category), false)
            ->assertSee('name="_method" value="PUT"', false)
            ->assertSee('value="'.$parent->id.'"', false)
            ->assertSee('value="By Fabric"', false)
            ->assertSee('Sarees', false)
            ->assertSee('name="meta_keywords"', false)
            ->assertSee('name="summary"', false)
            ->assertSee('sarees-by-fabric-edit-test', false)
            ->assertDontSee('Pure Silk', false);
    }
}
