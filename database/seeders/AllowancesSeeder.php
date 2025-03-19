<?php

namespace Database\Seeders;

use App\Models\Allowance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AllowancesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allowances = [
            'House Allowance',
            'Transport Allowance',
            'Medical Allowance',
        ];

        foreach ($allowances as $allowance) {
            Allowance::create(['name' => $allowance]);
        }
    }
}