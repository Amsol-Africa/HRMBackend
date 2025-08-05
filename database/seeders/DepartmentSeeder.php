<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            ['business_id' => 1, 'name' => 'IT Department', 'slug' => 'it-department', 'description' => 'Responsible for all IT-related tasks'],
            ['business_id' => 1, 'name' => 'Human Resources', 'slug' => 'human-resources', 'description' => 'Handles recruitment, employee benefits, and other HR tasks'],
            ['business_id' => 1, 'name' => 'Sales Department', 'slug' => 'sales-department', 'description' => 'Responsible for sales and customer interactions'],
            ['business_id' => 1, 'name' => 'Marketing Department', 'slug' => 'marketing-department', 'description' => 'Oversees marketing and branding strategies'],
            ['business_id' => 1, 'name' => 'Operations', 'slug' => 'operations', 'description' => 'Manages day-to-day operations of the business'],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
