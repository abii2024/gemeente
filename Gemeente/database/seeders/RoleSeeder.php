<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'view admin dashboard',
            'manage complaints',
            'view complaints',
            'edit complaint status',
            'delete complaints',
            'add notes',
            'delete notes',
            'view map',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $medewerkerRole = Role::create(['name' => 'medewerker']);

        // Assign all permissions to admin
        $adminRole->givePermissionTo(Permission::all());

        // Assign limited permissions to medewerker
        $medewerkerRole->givePermissionTo([
            'view admin dashboard',
            'view complaints',
            'edit complaint status',
            'add notes',
            'view map',
        ]);
    }
}
