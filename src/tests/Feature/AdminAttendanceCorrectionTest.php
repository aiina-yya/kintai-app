<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;


class AdminAttendanceCorrectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_correction_requests_are_displayed()
    {
        $admin = Admin::factory()->create();

        $user = User::factory()->create([
            'name' => '山田太郎',
        ]);


        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);


        AttendanceCorrection::factory()->create([
            'attendance_id' => $attendance->id,
            'is_approved' => false,
            'reason' => '修正理由',
        ]);


        $response = $this->actingAs($admin, 'admin')
            ->get(route('correction.list'));


        $response->assertStatus(200);

        $response->assertSee($user->name);
        $response->assertSee('修正理由');
    }

    public function test_approved_correction_requests_are_displayed()
    {
        $admin = Admin::factory()->create();

        $user = User::factory()->create([
            'name' => '佐藤花子',
        ]);


        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);


        AttendanceCorrection::factory()->create([
            'attendance_id' => $attendance->id,
            'is_approved' => true,
            'reason' => '時間修正',
        ]);


        $response = $this->actingAs($admin, 'admin')
            ->get(route('correction.list', ['status' => 'approved']));

        $response->assertStatus(200);

        $response->assertSee($user->name);
        $response->assertSee('時間修正');
    }

    public function test_correction_request_detail_is_displayed()
    {
        $admin = Admin::factory()->create();

        $user = User::factory()->create();


        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);


        $correction = AttendanceCorrection::factory()->create([
            'attendance_id' => $attendance->id,
            'requested_clock_in' => '10:00:00',
            'requested_clock_out' => '19:00:00',
            'reason' => '出勤時間修正',
            'is_approved' => false,
        ]);


        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.approve.view', [
                'attendance_correction_request_id' => $correction->id,
            ]));


        $response->assertStatus(200);

        $response->assertSee('出勤時間修正');
    }

    public function test_correction_request_can_be_approved()
    {
        $admin = Admin::factory()->create();

        $user = User::factory()->create();


        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);


        $correction = AttendanceCorrection::factory()->create([
            'attendance_id' => $attendance->id,
            'requested_clock_in' => '10:00:00',
            'requested_clock_out' => '19:00:00',
            'is_approved' => false,
        ]);


        $response = $this->actingAs($admin, 'admin')
            ->patch(route('admin.approve', [
                'attendance_correction_request_id' => $correction->id,
            ]));


        $response->assertRedirect(
            route('correction.list')
        );


        $this->assertDatabaseHas('attendance_corrections', [
            'id' => $correction->id,
            'is_approved' => true,
        ]);


        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'clock_in' => $attendance->work_date->format('Y-m-d').' 10:00:00',
            'clock_out' => $attendance->work_date->format('Y-m-d').' 19:00:00',
        ]);
    }
}
