<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_creation_saves_mobile_role_id_and_spatie_role_assignment(): void
    {
        $createUsersPermission = Permission::create([
            'name' => 'users.create',
            'guard_name' => 'admin',
        ]);
        $role = Role::create([
            'name' => 'Inventory Manager',
            'guard_name' => 'admin',
        ]);
        $currentAdmin = Admin::create([
            'name' => 'User Manager',
            'email' => 'user-manager@example.com',
            'password' => 'password',
            'is_active' => true,
        ]);
        $currentAdmin->givePermissionTo($createUsersPermission);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $response = $this->actingAs($currentAdmin, 'admin')
            ->post(route('admin.users.store'), [
                'name' => 'New Administrator',
                'email' => 'new-admin@example.com',
                'mobile' => '+15551234567',
                'password' => 'secure-password-123',
                'password_confirmation' => 'secure-password-123',
                'is_active' => '1',
                'role_id' => $role->id,
            ]);

        $response->assertRedirectToRoute('admin.users.index');

        $createdAdmin = Admin::query()
            ->where('email', 'new-admin@example.com')
            ->firstOrFail();

        $this->assertDatabaseHas('admins', [
            'id' => $createdAdmin->id,
            'mobile' => '+15551234567',
            'role_id' => $role->id,
        ]);
        $this->assertTrue($createdAdmin->hasRole($role));

    }
}
