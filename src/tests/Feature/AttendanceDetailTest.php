<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceBreak;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_name_is_displayed()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance.detail', $attendance));

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    public function test_work_date_is_displayed()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => '2026-07-20',
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance.detail', $attendance));

        $response->assertSee('2026年');
        $response->assertSee('7月20日');
    }

    public function test_clock_in_and_clock_out_are_displayed()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_in' => now()->setTime(9, 0),
            'clock_out' => now()->setTime(18, 0),
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance.detail', $attendance));

        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    public function test_break_time_is_displayed()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        AttendanceBreak::factory()->create([
            'attendance_id' => $attendance->id,
            'break_start' => now()->setTime(12, 0),
            'break_end' => now()->setTime(13, 0),
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance.detail', $attendance));

        $response->assertSee('12:00');
        $response->assertSee('13:00');
    }

}
