<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'items.view',
            'items.create',
            'items.edit',
            'items.delete',
            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete',
            'forms.view',
            'forms.create',
            'forms.edit',
            'forms.delete',
            'forms.submit',
            'workflows.view',
            'workflows.create',
            'workflows.edit',
            'workflows.delete',
            'workflows.transition',
            'users.view',
            'users.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        $editor = Role::firstOrCreate(['name' => 'editor']);
        $editor->syncPermissions([
            'items.view',
            'items.create',
            'items.edit',
            'categories.view',
            'categories.create',
            'categories.edit',
            'forms.view',
            'forms.create',
            'forms.edit',
            'forms.submit',
            'workflows.view',
            'workflows.transition',
        ]);

        $viewer = Role::firstOrCreate(['name' => 'viewer']);
        $viewer->syncPermissions([
            'items.view',
            'categories.view',
            'forms.view',
            'forms.submit',
            'workflows.view',
        ]);
    }
}
