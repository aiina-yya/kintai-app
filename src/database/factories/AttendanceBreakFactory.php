<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attendance;

class AttendanceBreakFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $start = now()->setTime(12,0);

        return [
            'attendance_id' => Attendance::factory(),
            'break_start' => $start,
            'break_end' => $start->copy()->addHour(),
        ];
    }
}
