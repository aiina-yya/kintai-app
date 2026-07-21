<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Attendance;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_all_users_attendance()
    {
        $admin = Admin::factory()->create();

        $user1 = User::factory()->create([
            'name' => '山田太郎',
        ]);

        $user2 = User::factory()->create([
            'name' => '佐藤花子',
        ]);


        Attendance::factory()->create([
            'user_id' => $user1->id,
            'work_date' => today(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        Attendance::factory()->create([
            'user_id' => $user2->id,
            'work_date' => today(),
            'clock_in' => '10:00:00',
            'clock_out' => '17:00:00',
        ]);


        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.attendance.list'));


        $response->assertStatus(200);

        $response->assertSee($user1->name);
        $response->assertSee($user2->name);

        $response->assertSee('09:00');
        $response->assertSee('18:00');

        $response->assertSee('10:00');
        $response->assertSee('17:00');
    }

    public function test_current_date_is_displayed()
    {
        $admin = Admin::factory()->create();


        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.attendance.list'));


        $response->assertStatus(200);

        $response->assertSee(
            today()->format('Y年n月j日')
        );
    }

    public function test_previous_day_attendance_is_displayed()
    {
        $admin = Admin::factory()->create();

        $user = User::factory()->create();


        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => today()->subDay(),
        ]);


        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.attendance.list', [
                'date' => today()->subDay()->toDateString(),
            ]));


        $response->assertStatus(200);

        $response->assertSee(
            today()->subDay()->format('Y年n月j日')
        );

        $response->assertSee($user->name);
    }

    public function test_next_day_attendance_is_displayed()
    {
        $admin = Admin::factory()->create();

        $user = User::factory()->create();


        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => today()->addDay(),
        ]);


        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.attendance.list', [
                'date' => today()->addDay()->toDateString(),
            ]));


        $response->assertStatus(200);

        $response->assertSee(
            today()->addDay()->format('Y年n月j日')
        );

        $response->assertSee($user->name);
    }
}
