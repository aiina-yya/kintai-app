<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceBreak;

class BreakTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_start_break()
    {
        $user = User::factory()->create();

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => now(),
        ]);

        $this->actingAs($user)
            ->post(route('attendance.breakStart'));

        $response = $this->actingAs($user)
            ->get(route('attendance'));

        $response->assertSee('休憩中');

        $this->assertDatabaseHas('attendance_breaks', [
            'attendance_id' => Attendance::first()->id,
        ]);
    }

    public function test_user_can_start_break_multiple_times()
    {
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => now(),
        ]);

        AttendanceBreak::create([
            'attendance_id' => $attendance->id,
            'break_start' => now()->subMinutes(10),
            'break_end' => now(),
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance'));

        $response->assertSee('休憩入');
    }

    public function test_user_can_end_break()
    {
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => now(),
        ]);

        AttendanceBreak::create([
            'attendance_id' => $attendance->id,
            'break_start' => now()->subMinutes(10),
        ]);

        $this->actingAs($user)
            ->post(route('attendance.breakEnd'));

        $response = $this->actingAs($user)
            ->get(route('attendance'));

        $response->assertSee('出勤中');
    }

    public function test_user_can_end_break_multiple_times()
    {
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => now(),
        ]);

        AttendanceBreak::create([
            'attendance_id' => $attendance->id,
            'break_start' => now()->subMinutes(10),
            'break_end' => now(),
        ]);

        $this->actingAs($user)->post(route('attendance.breakStart'));

        $this->actingAs($user)
        ->post(route('attendance.breakEnd'));

        $this->actingAs($user)->post(route('attendance.breakStart'));

        $response = $this->actingAs($user)
        ->get(route('attendance'));

        $response->assertSee('休憩戻');
    }

    public function test_break_time_is_displayed_on_attendance_list()
    {
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => now(),
        ]);

        AttendanceBreak::create([
            'attendance_id' => $attendance->id,
            'break_start' => now()->subMinutes(30),
            'break_end' => now()->subMinutes(15),
        ]);

            $response = $this->actingAs($user)
                ->get(route('attendance.list'));

            $response->assertSee('0:15');
    }
}
