<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Attendance;

class AdminAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_detail_data_is_displayed()
    {
        $admin = Admin::factory()->create();

        $user = User::factory()->create([
            'name' => '山田太郎',
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);


        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.attendance.detail', $attendance));


        $response->assertStatus(200);

        $response->assertSee($user->name);
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    public function test_clock_in_after_clock_out_validation_error()
    {
        $admin = Admin::factory()->create();

        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);


        $response = $this->actingAs($admin, 'admin')
            ->put(route('admin.attendance.update', $attendance->id), [
                'clock_in' => '19:00',
                'clock_out' => '18:00',
                'note' => '修正理由',

                'break_ids' => [],
                'break_start' => [],
                'break_end' => [],
            ]);


        $response->assertSessionHasErrors([
            'clock_in' => '出勤時間もしくは退勤時間が不適切な値です'
        ]);
    }

    public function test_break_start_after_clock_out_validation_error()
    {
        $admin = Admin::factory()->create();

        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_out' => '18:00:00',
        ]);


        $response = $this->actingAs($admin, 'admin')
            ->put(route('admin.attendance.update', $attendance), [
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'break_start' => '19:00',
                'break_end' => '19:30',
                'note' => '修正理由',
            ]);


        $response->assertSessionHasErrors([
            'break_start'
        ]);
    }

    public function test_break_end_after_clock_out_validation_error()
    {
        $admin = Admin::factory()->create();

        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);


        $response = $this->actingAs($admin, 'admin')
            ->put(route('admin.attendance.update', $attendance), [
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'break_start' => '12:00',
                'break_end' => '19:00',
                'note' => '修正理由',
            ]);


        $response->assertSessionHasErrors([
            'break_end'
        ]);
    }

    public function test_note_required_validation_error()
    {
        $admin = Admin::factory()->create();

        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);


        $response = $this->actingAs($admin, 'admin')
            ->put(route('admin.attendance.update', $attendance), [
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'note' => '',
            ]);


        $response->assertSessionHasErrors([
            'note'
        ]);
    }
}
