<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;


class ClockInTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_clock_in()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('attendance.clockIn'));

        $response->assertRedirect(route('attendance'));

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'work_date' => today(),
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance'));

        $response->assertSee('出勤中');
    }

    public function test_clock_in_button_is_not_displayed_after_clock_out()
    {
        $user = User::factory()->create();

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => now()->subHours(8),
            'clock_out' => now(),
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance'));

        $response->assertDontSee('出勤');
    }

    public function test_clock_in_time_is_displayed_on_attendance_list()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('attendance.clockIn'));

        $response = $this->actingAs($user)
            ->get(route('attendance.list'));

        $response->assertSee(now()->format('H:i'));
    }

}
