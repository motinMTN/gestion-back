<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Crear permisos
        $permissions = [
            'manage users',
            'manage distributors',
            'manage practices',
            'manage branches',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles y asignar permisos
        $roles = [
            'generalAdmin' => ['manage users', 'manage distributors', 'manage practices', 'manage branches'],
            'distributorsAdmin' => ['manage distributors'],
            'practicesAdmin' => ['manage practices'],
            'branchesAdmin' => ['manage branches'],
        ];

        foreach ($roles as $role => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $role]);
            $role->syncPermissions($rolePermissions); // Asigna los permisos al rol, asegurando que no se dupliquen
        }
    }
}

