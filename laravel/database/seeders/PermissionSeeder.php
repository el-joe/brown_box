<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    public const GROUPS = [
        'Categories' => ['categories.view', 'categories.create', 'categories.edit', 'categories.delete'],
        'Brands' => ['brands.view', 'brands.create', 'brands.edit', 'brands.delete'],
        'Products' => ['products.view', 'products.create', 'products.edit', 'products.delete'],
        'Inventory' => ['inventory.view', 'inventory.adjust', 'purchases.view', 'purchases.create'],
        'Orders' => ['orders.view', 'orders.edit', 'orders.status', 'orders.verify_payment'],
        'Customers' => ['customers.view', 'customers.edit'],
        'Marketing' => ['coupons.view', 'coupons.create', 'coupons.edit', 'coupons.delete', 'flash_sales.view', 'flash_sales.manage'],
        'Affiliates' => ['affiliates.view', 'affiliates.manage'],
        'Finance' => ['finance.view', 'finance.create', 'finance.edit'],
        'Settings' => ['settings.view', 'settings.edit'],
        'Admins' => ['admins.view', 'admins.manage', 'roles.manage'],
        'Reports' => ['reports.view'],
        'Audit' => ['audit.view'],
    ];

    public function run(): void
    {
        foreach (self::GROUPS as $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
            }
        }

        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'admin']);
        $role->syncPermissions(Permission::where('guard_name', 'admin')->get());
    }
}
