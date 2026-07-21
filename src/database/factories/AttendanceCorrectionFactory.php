<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;

class AttendanceCorrectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'attendance_id' => Attendance::factory(),
            'requested_clock_in' => now()->setTime(9, 0),
            'requested_clock_out' => now()->setTime(18, 0),
            'reason' => '電車遅延',
            'is_approved' => false,
        ];
    }

    public function approved()
    {
        return $this->state(fn () => [
            'is_approved' => true,
        ]);
    }
}
