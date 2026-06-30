<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

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
            $attendances = $user->attendances()->latest('work_date')->take(5)->get();

            foreach ($attendances as $index => $attendance) {
                $date = $attendance->work_date->copy();

                $attendance->correctionRequest()->create([
                    'requested_clock_in' => $date->copy()->setTime(8,50),
                    'requested_clock_out' => $date->copy()->setTime(18,10),
                    'reason' => '電車遅延のため',
                    'is_approved' => $index >= 3,
                ]);
            }
        }


    }
}
