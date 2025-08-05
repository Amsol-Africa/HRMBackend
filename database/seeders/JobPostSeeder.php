<?php

namespace Database\Seeders;

use App\Models\JobPost;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JobPostSeeder extends Seeder
{
    public function run(): void
    {
        JobPost::factory(10)->create();
    }
}
