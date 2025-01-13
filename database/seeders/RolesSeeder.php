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
            'superadmin',
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
    }
}
