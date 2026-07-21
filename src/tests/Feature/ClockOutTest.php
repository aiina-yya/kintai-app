<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;


class ClockOutTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_clock_out()
    {
        $user = User::factory()->create();

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => now()->subHours(8),
        ]);

        $response = $this->actingAs($user)
            ->post(route('attendance.clockOut'));

        $response->assertRedirect(route('attendance'));

        $response = $this->actingAs($user)
            ->get(route('attendance'));

        $response->assertSee('退勤済');
        $response->assertSee('お疲れ様でした。');
    }

    public function test_clock_out_time_is_displayed_on_attendance_list()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('attendance.clockIn'));

        $this->actingAs($user)
            ->post(route('attendance.clockOut'));

        $attendance = Attendance::where('user_id', $user->id)->first();

        $response = $this->actingAs($user)
            ->get(route('attendance.list'));

        $response->assertSee($attendance->clock_out->format('H:i'));
    }
}

