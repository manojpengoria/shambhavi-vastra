<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $permissions = [
            'dashboard.view',
            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete',
            'products.view',
            'products.create',
            'products.edit',
            'products.delete',
            'orders.view',
            'orders.edit',
            'customers.view',
            'customers.edit',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'admins.view',
            'admins.create',
            'admins.edit',
            'admins.delete',
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate(
                $permission,
                'web'
            );
        }

        $superAdmin = Role::findOrCreate(
            'Super Admin',
            'web'
        );

        $manager = Role::findOrCreate(
            'Manager',
            'web'
        );

        $productStaff = Role::findOrCreate(
            'Product Staff',
            'web'
        );

        $seoStaff = Role::findOrCreate(
            'SEO Staff',
            'web'
        );

        $superAdmin->syncPermissions(
            Permission::where('guard_name', 'web')->get()
        );

        $manager->syncPermissions([
            'dashboard.view',
            'categories.view',
            'categories.create',
            'categories.edit',
            'products.view',
            'products.create',
            'products.edit',
            'orders.view',
            'orders.edit',
            'customers.view',
            'customers.edit',
        ]);

        $productStaff->syncPermissions([
            'dashboard.view',
            'categories.view',
            'categories.create',
            'categories.edit',
            'products.view',
            'products.create',
            'products.edit',
        ]);

        $seoStaff->syncPermissions([
            'dashboard.view',
            'categories.view',
            'products.view',
            'products.edit',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
