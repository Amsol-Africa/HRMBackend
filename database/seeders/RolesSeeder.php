<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $roles = [
            'super-admin',
            'admin',
            'hr',
            'finance',
            'it',
            'employee',
            'marketing',
            'business-admin',
            'business-hr',
            'business-finance',
            'business-it',
            'business-employee',
            'business-marketing'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Create permissions
        $deleteLeadPermission = Permission::firstOrCreate(['name' => 'delete-lead', 'guard_name' => 'web']);
        $deleteContactSubmissionPermission = Permission::firstOrCreate(['name' => 'delete-contact-submission', 'guard_name' => 'web']);

        // Assign permissions to 'admin' and 'it' roles
        $adminRole = Role::findByName('admin', 'web');
        $itRole = Role::findByName('it', 'web');

        $adminRole->givePermissionTo([$deleteLeadPermission, $deleteContactSubmissionPermission]);
        $itRole->givePermissionTo([$deleteLeadPermission, $deleteContactSubmissionPermission]);
    }
}
