<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class AttendancePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_date_and_time_are_displayed()
    {
        $user = User::factory()->create();

    $response = $this->actingAs($user)
                    ->get('/attendance');

    $response->assertStatus(200);
    }
}