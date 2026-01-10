<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Create Admin role
        $admin = Role::firstOrCreate(['name' => 'admin']);

        // Assign ALL permissions
        $admin->syncPermissions(Permission::all());
    }
}
