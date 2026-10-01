<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Define all permissions used in your sidebar and routes
        $permissions = [
            // User & Access Management
            'manage-users',

            // Customer Accounts
            'manage-customers',

            // Store Settings & Locations
            'manage-settings',
            'manage-governorates',
            'manage-areas',
            'manage-stores',

            // Catalog Management
            'manage-products',
            'manage-categories',

            // Order Fulfillment
            'manage-orders',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name'       => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        // 3. Create Roles and assign permissions

        // --- Role 1: Super Admin (Has everything) ---
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // --- Role 2: Store Manager ---
        $manager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $manager->syncPermissions([
            'manage-orders',
            'manage-products',
            'manage-categories',
            'manage-customers',
            'manage-settings',
            'manage-governorates',
            'manage-areas',
            'manage-pickstores',
        ]);

        // --- Role 3: Kitchen / Order Staff ---
        $orderStaff = Role::firstOrCreate(['name' => 'Order Staff', 'guard_name' => 'web']);
        $orderStaff->syncPermissions([
            'manage-orders',
        ]);

        // --- Role 4: Inventory / Catalog Staff ---
        $inventoryStaff = Role::firstOrCreate(['name' => 'Catalog Staff', 'guard_name' => 'web']);
        $inventoryStaff->syncPermissions([
            'manage-products',
            'manage-categories',
        ]);

        // 4. Create or update Default Super Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@otherwise.com'],
            [
                'name'     => 'System Admin',
                'password' => Hash::make('password123'),
            ]
        );

        // Assign the Super Admin role
        $admin->syncRoles([$superAdmin]);
    }
}
