<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\AttendanceCorrection;
use App\Models\AttendanceCorrectionBreak;

class AttendanceCorrectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::with('attendance.breaks')->get();

        foreach ($users as $user) {
            $attendances = $user->attendance()->latest('work_date')->take(5)->get();

            foreach ($attendances as $index => $attendance) {
                $date = $attendance->work_date->copy();
                $correction = AttendanceCorrection::updateOrCreate(
                    ['attendance_id' => $attendance->id],
                    [
                        'requested_clock_in' => $date->copy()->setTime(8,0),
                        'requested_clock_out' => $date->copy()->setTime(18,0),
                        'reason' => '電車遅延のため',
                        'is_approved' => $index >= 3,
                    ]);

                    foreach($attendance->breaks as $break) {
                    AttendanceCorrectionBreak::updateOrCreate(
                        ['attendance_correction_id' => $correction->id,
                        'attendance_break_id' => $break->id],
                        [
                            'requested_break_start' => $date->copy()->setTime(12,0),
                            'requested_break_end' => $date->copy()->setTime(13,0),
                        ]);
                }
            }
        }
    }
}