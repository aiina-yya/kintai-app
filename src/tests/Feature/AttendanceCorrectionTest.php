<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceBreak;
use App\Models\AttendanceCorrection;


class AttendanceCorrectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_clock_in_cannot_be_after_clock_out()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->from(route('attendance.detail', $attendance))
            ->post(route('attendance.correction', $attendance), [
                'clock_in' => '19:00',
                'clock_out' => '18:00',
                'break_start' => ['12:00'],
                'break_end' => ['13:00'],
                'reason' => 'テスト',
                'break_ids' => [],
            ]);

        $response->assertSessionHasErrors([
            'clock_in' => '出勤時間もしくは退勤時間が不適切な値です',
        ]);
    }

    public function test_break_start_cannot_be_after_clock_out()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->post(route('attendance.correction', $attendance),[
                'clock_in'=>'09:00',
                'clock_out'=>'18:00',
                'break_start'=>['19:00'],
                'break_end'=>['19:30'],
                'reason'=>'テスト',
                'break_ids'=>[],
            ]);

        $response->assertSessionHasErrors([
            'break_end.0'=>'休憩時間が不適切な値です',
        ]);
    }

    public function test_break_end_cannot_be_after_clock_out()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id'=>$user->id,
        ]);

        $response = $this->actingAs($user)
            ->post(route('attendance.correction',$attendance),[
                'clock_in'=>'09:00',
                'clock_out'=>'18:00',
                'break_start'=>['17:00'],
                'break_end'=>['19:00'],
                'reason'=>'テスト',
                'break_ids'=>[],
            ]);

        $response->assertSessionHasErrors([
            'break_end.0'=>'休憩時間もしくは退勤時間が不適切な値です',
        ]);
    }

    public function test_reason_is_required()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id'=>$user->id,
        ]);

        $response = $this->actingAs($user)
            ->post(route('attendance.correction',$attendance),[
                'clock_in'=>'09:00',
                'clock_out'=>'18:00',
                'break_start'=>['12:00'],
                'break_end'=>['13:00'],
                'reason'=>'',
                'break_ids'=>[],
            ]);

        $response->assertSessionHasErrors([
            'reason'=>'備考を記入してください',
        ]);
    }

    public function test_correction_request_is_created()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id'=>$user->id,
        ]);

        $this->actingAs($user)
            ->post(route('attendance.correction',$attendance),[
                'clock_in'=>'09:00',
                'clock_out'=>'18:00',
                'break_start'=>['12:00'],
                'break_end'=>['13:00'],
                'reason'=>'電車遅延',
                'break_ids'=>[],
            ]);

        $this->assertDatabaseHas('attendance_corrections',[
            'attendance_id'=>$attendance->id,
            'reason'=>'電車遅延',
            'is_approved'=>false,
        ]);
    }

    public function test_pending_correction_is_displayed()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $correction = AttendanceCorrection::factory()->create([
            'attendance_id' => $attendance->id,
            'reason' => '電車遅延',
            'is_approved' => false,
        ]);

        $response = $this->actingAs($user)
            ->get(route('correction.list'));

        $response->assertStatus(200);

        $response->assertSee('電車遅延');
    }

    public function test_approved_correction_is_displayed()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        AttendanceCorrection::factory()->create([
            'attendance_id' => $attendance->id,
            'reason' => '電車遅延',
            'is_approved' => true,
        ]);

        $response = $this->actingAs($user)
            ->get(route('correction.list', [
                'status' => 'approved'
            ]));

        $response->assertStatus(200);

        $response->assertSee('電車遅延');
    }

    public function test_user_can_open_correction_detail()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $correction = AttendanceCorrection::factory()->create([
            'attendance_id' => $attendance->id,
            'is_approved' => false,
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance.detail', [
                'attendance' => $attendance->id,
                'correction' => $correction->id,
            ]));

        $response->assertStatus(200);
    }
}
