<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
class AdminStaffTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_all_users_information()
    {
        $admin = Admin::factory()->create();

        $user1 = User::factory()->create([
            'name' => '山田太郎',
            'email' => 'yamada@example.com',
        ]);

        $user2 = User::factory()->create([
            'name' => '佐藤花子',
            'email' => 'sato@example.com',
        ]);


        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.staff'));


        $response->assertStatus(200);

        $response->assertSee($user1->name);
        $response->assertSee($user1->email);

        $response->assertSee($user2->name);
        $response->assertSee($user2->email);
    }

    public function test_selected_user_attendance_is_displayed()
    {
        $admin = Admin::factory()->create();

        $user = User::factory()->create([
            'name' => '山田太郎',
        ]);


        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => '2026-07-01',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);


        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.attendance.staff', $user->id));


        $response->assertStatus(200);

        $response->assertSee($user->name);
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    public function test_previous_month_attendance_is_displayed()
    {
        $admin = Admin::factory()->create();

        $user = User::factory()->create();


        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => '2026-06-15',
        ]);


        $response = $this->actingAs($admin,'admin')
            ->get(route('admin.attendance.staff', [
                'id' => $user->id,
                'year' => 2026,
                'month' => 6,
            ]));


        $response->assertStatus(200);

        $response->assertSee('2026');
        $response->assertSee('06');
    }

    public function test_next_month_attendance_is_displayed()
    {
        $admin = Admin::factory()->create();

        $user = User::factory()->create();


        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => '2026-08-15',
        ]);


        $response = $this->actingAs($admin,'admin')
            ->get(route('admin.attendance.staff', [
                'id' => $user->id,
                'year' => 2026,
                'month' => 8,
            ]));


        $response->assertStatus(200);

        $response->assertSee('2026');
        $response->assertSee('08');
    }

    public function test_detail_button_redirects_to_attendance_detail()
    {
        $admin = Admin::factory()->create();

        $user = User::factory()->create();


        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);


        $response = $this->actingAs($admin,'admin')
            ->get(route('admin.attendance.detail', $attendance->id));


        $response->assertStatus(200);

        $response->assertSee($user->name);
    }
}
