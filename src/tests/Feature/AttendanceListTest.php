<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceBreak;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_see_all_of_their_attendances()
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 3; $i++) {
            Attendance::factory()->create([
                'user_id' => $user->id,
                'work_date' => today()->subDays($i),
            ]);
        }

        $response = $this->actingAs($user)
            ->get(route('attendance.list'));

        $response->assertStatus(200);

        foreach ($user->attendance as $attendance) {
            $response->assertSee($attendance->work_date->format('Y/m/d'));
        }
    }

    public function test_current_month_is_displayed()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('attendance.list'));

        $response->assertStatus(200);

        $response->assertSee(now()->format('Y/n'));
    }

    public function test_previous_month_is_displayed()
    {
        $user = User::factory()->create();

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->subMonth()->startOfMonth(),
            'clock_in' => now(),
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance.list', [
                'year' => now()->subMonth()->year,
                'month' => now()->subMonth()->month,
            ]));

        $response->assertSee(now()->subMonth()->format('Y/n'));
    }

    public function test_next_month_is_displayed()
    {
        $user = User::factory()->create();

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->addMonth()->startOfMonth(),
            'clock_in' => now(),
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance.list', [
                'year' => now()->addMonth()->year,
                'month' => now()->addMonth()->month,
            ]));

        $response->assertSee(now()->addMonth()->format('Y/n'));
    }

    public function test_user_can_open_attendance_detail()
    {
    $user = User::factory()->create();

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'work_date' => today(),
        'clock_in' => now(),
    ]);

    $response = $this->actingAs($user)
        ->get(route('attendance.detail', $attendance));

    $response->assertStatus(200);

    $response->assertSee('勤怠詳細');
    }
}
