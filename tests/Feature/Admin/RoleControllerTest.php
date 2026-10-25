<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_selected_permission_from_create_form_is_assigned_to_new_role(): void
    {
        $createRolePermission = Permission::create([
            'name' => 'roles.create',
            'guard_name' => 'admin',
        ]);
        $productPermission = Permission::create([
            'name' => 'products.view',
            'guard_name' => 'admin',
        ]);
        $admin = Admin::create([
            'name' => 'Role Manager',
            'email' => 'role-manager@example.com',
            'password' => 'password',
            'is_active' => true,
        ]);
        $admin->givePermissionTo($createRolePermission);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs($admin, 'admin')
            ->get(route('admin.roles.create'))
            ->assertOk()
            ->assertSee('name="permissions[]" value="products.view"', false);

        $response = $this->post(route('admin.roles.store'), [
            'name' => 'Inventory Manager',
            'permissions' => [$productPermission->name],
        ]);

        $response->assertRedirectToRoute('admin.roles.index');

        $role = Role::query()->where('name', 'Inventory Manager')->firstOrFail();

        $this->assertDatabaseHas('role_has_permissions', [
            'role_id' => $role->id,
            'permission_id' => $productPermission->id,
        ]);
    }
}
