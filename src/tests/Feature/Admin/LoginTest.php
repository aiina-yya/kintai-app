<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_is_required_for_login()
    {
        $response = $this->post('/admin/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_password_is_required_for_login()
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_user_cannot_login_with_unregistered_email()
    {
        $response = $this->from('/admin/login')->post('/admin/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/login');

        $response->assertSessionHasErrors([
            'email',
        ]);

        $this->assertGuest();

        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません。'
        ]);
    }

    public function test_admin_can_login()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated('admin');

        $response->assertRedirect('admin/attendance/list');
    }
}


