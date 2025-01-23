<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ShiftSeeder extends Seeder
{
    public function run()
    {
        $shifts = [
            ['business_id' => 1, 'name' => 'Morning Shift', 'slug' => 'morning-shift', 'start_time' => '08:00', 'end_time' => '16:00', 'description' => 'Standard morning shift', 'is_active' => true],
            ['business_id' => 1, 'name' => 'Afternoon Shift', 'slug' => 'afternoon-shift', 'start_time' => '16:00', 'end_time' => '00:00', 'description' => 'Standard afternoon shift', 'is_active' => true],
            ['business_id' => 1, 'name' => 'Night Shift', 'slug' => 'night-shift', 'start_time' => '00:00', 'end_time' => '08:00', 'description' => 'Standard night shift', 'is_active' => true],
            ['business_id' => 1, 'name' => 'Flexible Shift', 'slug' => 'flexible-shift', 'start_time' => '10:00', 'end_time' => '18:00', 'description' => 'Flexible shift for employees', 'is_active' => true],
            ['business_id' => 1, 'name' => 'Weekend Shift', 'slug' => 'weekend-shift', 'start_time' => '09:00', 'end_time' => '17:00', 'description' => 'Shift during weekends', 'is_active' => true],
        ];

        foreach ($shifts as $shift) {
            Shift::create($shift);
        }
    }
}
