<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $resources = ['users', 'roles', 'doctors', 'patients', 'services'];
        $actions = ['view', 'create', 'update', 'delete'];

        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$action}_{$resource}",
                    'guard_name' => 'web'
                ]);
            }
        }

        $pages = ['dashboard', 'ManageReservations','Settings'];
        foreach ($pages as $page) {
            Permission::firstOrCreate([
                'name' => "view_{$page}",
                'guard_name' => 'web'
            ]);
        }

        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $permissions = Permission::all();
        $role->syncPermissions($permissions);
    }
}
