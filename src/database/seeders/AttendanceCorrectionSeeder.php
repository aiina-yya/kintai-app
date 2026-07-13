<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\AttendanceCorrection;

class AttendanceCorrectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::with('attendance')->get();

        foreach ($users as $user) {
            $attendances = $user->attendance()->latest('work_date')->take(5)->get();

            foreach ($attendances as $index => $attendance) {
                $date = $attendance->work_date->copy();

                AttendanceCorrection::updateOrCreate(
                    ['attendance_id' => $attendance->id],
                    [
                        'requested_clock_in' => $date->copy()->setTime(8,50),
                        'requested_clock_out' => $date->copy()->setTime(18,10),
                        'requested_break_start' => $date->copy()->setTime(12,30),
                        'requested_break_end' => $date->copy()->setTime(13,30),
                        'is_approved' => $index >= 3,
                    ]);
            }
        }
    }
}
