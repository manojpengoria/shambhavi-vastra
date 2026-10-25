<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class AdminRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guard = 'admin';
        app()[PermissionRegistrar::class]
            ->forgetCachedPermissions();

        $permissions = [
            // Dashboard
            'dashboard.view',
            // Products
            'products.view',
            'products.create',
            'products.edit',
            'products.delete',
            // Categories
            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete',
            // Orders
            'orders.view',
            'orders.create',
            'orders.edit',
            'orders.cancel',
            // Customers
            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',
            // Inventory
            'inventory.view',
            'inventory.edit',
            // Reports
            'reports.view',
            'reports.export',
            // Admin Users
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            // Roles
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'roles.assign',
            // Settings
            'settings.view',
            'settings.edit',
        ];

        foreach ($permissions as $permissionName) {
            Permission::findOrCreate(
                $permissionName,
                $guard
            );
        }

        $allPermissions = Permission::where(
            'guard_name',
            $guard
        )->get();

        // Super Admin
        $superAdmin = Role::findOrCreate(
            'Super Admin',
            $guard
        );
        $superAdmin->syncPermissions($allPermissions);

        // Manager
        $manager = Role::findOrCreate(
            'Manager',
            $guard
        );
        $manager->syncPermissions([
            'dashboard.view',
            'products.view',
            'products.create',
            'products.edit',
            'categories.view',
            'categories.create',
            'categories.edit',
            'orders.view',
            'orders.edit',
            'customers.view',
            'customers.edit',
            'inventory.view',
            'inventory.edit',
            'reports.view',
        ]);

        // Product Staff
        $productStaff = Role::findOrCreate(
            'Product Staff',
            $guard
        );

        $productStaff->syncPermissions([
            'dashboard.view',
            'products.view',
            'products.create',
            'products.edit',
            'categories.view',
            'inventory.view',
        ]);

        // Order Staff
        $orderStaff = Role::findOrCreate(
            'Order Staff',
            $guard
        );

        $orderStaff->syncPermissions([
            'dashboard.view',
            'orders.view',
            'orders.edit',
            'orders.cancel',
            'customers.view',
        ]);

        app()[PermissionRegistrar::class]
            ->forgetCachedPermissions();
    }
}
