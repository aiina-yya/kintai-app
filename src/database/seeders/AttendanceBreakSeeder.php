<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;

class AttendanceBreakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $attendances = Attendance::all();

        foreach ($attendances as $attendance) {
            $date = $attendance->work_date->copy();

            $attendance->breaks()->firstOrCreate([
                    'break_start' => $date->copy()->setTime(12,0),
                    'break_end' => $date->copy()->setTime(13,0),
                ]);
        }
    }
}
