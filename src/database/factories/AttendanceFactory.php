<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Attendance;

class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'work_date' => now()->toDateString(),
            'clock_in' => now()->setTime(9,0),
            'clock_out' => now()->setTime(18,0),
            'work_minutes' => 480,
        ];
    }
}
