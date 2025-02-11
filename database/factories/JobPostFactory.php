<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\JobPost;
use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobPostFactory extends Factory
{
    protected $model = JobPost::class;

    public function definition(): array
    {
        return [
            'business_id' => Business::findBySlug(session('active_business_slug')->id),
            'created_by' => User::factory(),
            'title' => $this->faker->jobTitle(),
            'description' => $this->faker->paragraph(4),
            'requirements' => $this->faker->paragraph(3),
            'salary_range' => '50,000 - 80,000 KES',
            'employment_type' => $this->faker->randomElement(['full_time', 'part_time', 'contract', 'internship']),
            'location' => $this->faker->city(),
            'status' => $this->faker->randomElement(['open', 'closed', 'draft']),
            'posted_at' => now(),
            'closed_at' => null,
        ];
    }
}
