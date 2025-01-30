<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            // Owner company roles
            'super-admin',
            'admin',
            'hr',
            'finance',
            'it',
            'employee',

            // Business-specific roles (if different from owner company)
            'business-admin',
            'business-hr',
            'business-finance',
            'business-it',
            'business-employee',
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
    }
}
