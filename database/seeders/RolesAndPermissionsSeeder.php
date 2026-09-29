<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'manage-products',
            'manage-categories',
            'manage-orders',
            'manage-users',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Create Roles and assign permissions
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $manager->syncPermissions(['manage-products', 'manage-categories', 'manage-orders']);

        $staff = Role::firstOrCreate(['name' => 'Staff']);
        $staff->syncPermissions(['manage-orders']);

        // Assign Super Admin role to the first user or create one
        $admin = User::firstOrCreate(
            ['email' => 'admin@otherwise.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password123'),
            ]
        );
        $admin->assignRole($superAdmin);
    }
}
