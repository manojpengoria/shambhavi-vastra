<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
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
            // 'dashboard.view',
            // // Products
            // 'products.view',
            // 'products.create',
            // 'products.edit',
            // 'products.delete',
            // // Categories
            // 'categories.view',
            // 'categories.create',
            // 'categories.edit',
            // 'categories.delete',
            // // Orders
            // 'orders.view',
            // 'orders.create',
            // 'orders.edit',
            // 'orders.cancel',
            // // Customers
            // 'customers.view',
            // 'customers.create',
            // 'customers.edit',
            // 'customers.delete',
            // // Inventory
            // 'inventory.view',
            // 'inventory.edit',
            // // Reports
            // 'reports.view',
            // 'reports.export',
            // // Admin Users
            // 'users.view',
            // 'users.create',
            // 'users.edit',
            // 'users.delete',
            // // Roles
            // 'roles.view',
            // 'roles.create',
            // 'roles.edit',
            // 'roles.delete',
            // 'roles.assign',
            // // Settings
            // 'settings.view',
            // 'settings.edit',
            // Location Management
            // 'countries.view',
            // 'countries.create',
            // 'countries.edit',
            // 'countries.delete',
            // 'states.view',
            // 'states.create',
            // 'states.edit',
            // 'states.delete',
            // 'cities.view',
            // 'cities.create',
            // 'cities.edit',
            // 'cities.delete',
            // 'postal-codes.view',
            // 'postal-codes.create',
            // 'postal-codes.edit',
            // 'postal-codes.delete',
            // 'areas.view',
            // 'areas.create',
            // 'areas.edit',
            // 'areas.delete',
            // Shipping
            // 'warehouses.view',
            // 'warehouses.create',
            // 'warehouses.edit',
            // 'warehouses.delete',
            // 'courier-partners.view',
            // 'courier-partners.create',
            // 'courier-partners.edit',
            // 'courier-partners.delete',
            // 'shipping-methods.view',
            // 'shipping-methods.create',
            // 'shipping-methods.edit',
            // 'shipping-methods.delete',
            // 'shipping-zones.view',
            // 'shipping-zones.create',
            // 'shipping-zones.edit',
            // 'shipping-zones.delete',
            // 'shipping-packages.view',
            // 'shipping-packages.create',
            // 'shipping-packages.edit',
            // 'shipping-packages.delete',
            // 'shipping-classes.view',
            // 'shipping-classes.create',
            // 'shipping-classes.edit',
            // 'shipping-classes.delete',
            // 'shipping-settings.view',
            // 'shipping-settings.edit',
            // Size Charts
            'size-charts.view',
            'size-charts.create',
            'size-charts.edit',
            'size-charts.delete',
            // Payment Options
            // 'payment-options.view',
            // 'payment-options.edit',
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
