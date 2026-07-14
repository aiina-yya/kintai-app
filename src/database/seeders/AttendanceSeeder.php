<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\User;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();

        foreach ($users as $user) {

            for ($i = 0; $i < 20; $i++) {
                $date = Carbon::today()->subDays($i);

                Attendance::firstOrCreate([
                    'user_id' => $user->id,
                    'work_date' => $date->toDateString(),
                    ],
                    [
                        'clock_in' => $date->copy()->setTime(9,0),
                        'clock_out' => $date->copy()->setTime(18,0),
                        'work_minutes' => 480,
                        'reason' => null,
                    ]);
            }
        }
    }
}
