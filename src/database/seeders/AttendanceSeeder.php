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
    private function seedNormalUser(User $user)
    {
        $today = Carbon::today();

        $count = 0;
        
        $date = $today->copy()->subDay();


        while ($count < 10) {

            if (!$date->isWeekend()) {

                Attendance::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'work_date' => $date->toDateString(),
                    ],
                    [
                        'clock_in' => $date->copy()->setTime(9,0),
                        'clock_out' => $date->copy()->setTime(18,0),
                        'work_minutes' => 480,
                        'reason' => null,
                    ]
                );
                $count++;
            }
            $date->subDay();
        }
    }

    public function run()
    {
        $users = User::where('email', '!=', 'user1@example.com')
        ->get();

        foreach ($users as $user) {
            $this->seedNormalUser($user);
        }

        $user1 = User::where('email', 'user1@example.com')->first();

        $this->seedUser1($user1);

    }

    private function seedUser1(User $user)
    {
        $today = Carbon::today();

        for ($month = 5; $month >= 1; $month--) {

            $targetMonth = $today->copy()->subMonths($month);
            $current = $targetMonth->copy()->startOfMonth();

            $count = 0;


            while ($current->month == $targetMonth->month && $current->year == $targetMonth->year) {

                if (!$current->isWeekend() && $count < 15) {
                    Attendance::firstOrCreate(
                        [
                            'user_id' => $user->id,
                            'work_date' => $current->toDateString(),
                        ],

                        [
                            'clock_in' => $current->copy()->setTime(9, 0),
                            'clock_out' => $current->copy()->setTime(18, 0),
                            'work_minutes' => 480,
                            'reason' => null,
                        ]
                    );
                    $count++;
                }
                $current->addDay();
            }
        }
        $this->seedUser1CurrentMonth($user);
    }

    private function seedUser1CurrentMonth(User $user)
    {
        $dates = [];

        $current = Carbon::today()->startOfMonth();


        while (count($dates) < 17) {

            if (!$current->isWeekend()) {
                $dates[] = $current->copy();
            }

            $current->addDay();
        }

        foreach (array_slice($dates, 0, 10) as $date) {
            $this->createAttendance(
                $user,
                $date,
                '09:00',
                '18:00',
                480
            );
        }

        foreach (array_slice($dates, 10, 3) as $date) {
            $this->createAttendance(
                $user,
                $date,
                '09:00',
                '20:00',
                600
            );
        }

        foreach (array_slice($dates, 13, 2) as $date) {
            $this->createAttendance(
                $user,
                $date,
                '09:30',
                '18:00',
                510
            );
        }

        $this->createAttendance(
            $user,
            $dates[15],
            '09:00',
            '17:00',
            420
        );

        $this->createAttendance(
            $user,
            $dates[16],
            '08:00',
            '21:00',
            720
        );
    }

    private function createAttendance(
        User $user,
        Carbon $date,
        string $clockIn,
        string $clockOut,
        int $minutes
    )
    {
        Attendance::firstOrCreate([
            'user_id' => $user->id,
            'work_date' => $date->toDateString(),
        ],
        [
            'clock_in' => $date->copy()->setTimeFromTimeString($clockIn),
            'clock_out' => $date->copy()->setTimeFromTimeString($clockOut),
            'work_minutes' => $minutes,
            'reason' => null,
        ]);
    }
}