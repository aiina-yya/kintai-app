<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            AttendanceSeeder::class,
            AttendanceBreakSeeder::class,
            AttendanceCorrectionSeeder::class,
            AdminSeeder::class,
            UserSeeder::class,
        ]);
    }
}
