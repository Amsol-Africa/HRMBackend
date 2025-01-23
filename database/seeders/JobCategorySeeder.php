<?php

namespace Database\Seeders;

use App\Models\JobCategory;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JobCategorySeeder extends Seeder
{
    public function run()
    {
        $jobCategories = [
            ['business_id' => 1, 'name' => 'Software Developer', 'slug' => 'software-developer', 'description' => 'Develops and maintains software applications'],
            ['business_id' => 1, 'name' => 'Human Resources', 'slug' => 'human-resources', 'description' => 'Manages employee relations and recruitment'],
            ['business_id' => 1, 'name' => 'Marketing Manager', 'slug' => 'marketing-manager', 'description' => 'Oversees marketing strategies and campaigns'],
            ['business_id' => 1, 'name' => 'Project Manager', 'slug' => 'project-manager', 'description' => 'Manages and coordinates project teams'],
            ['business_id' => 1, 'name' => 'Sales Executive', 'slug' => 'sales-executive', 'description' => 'Responsible for sales and client acquisition'],
        ];

        foreach ($jobCategories as $category) {
            JobCategory::create($category);
        }
    }
}
