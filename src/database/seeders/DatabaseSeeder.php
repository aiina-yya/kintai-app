<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            AdminSeeder::class,
            AttendanceSeeder::class,
            AttendanceBreakSeeder::class,
            AttendanceCorrectionSeeder::class,
        ]);
    }
}
