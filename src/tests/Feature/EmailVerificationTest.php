<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_email_is_sent_after_registration()
        {
        Notification::fake();


        $response = $this->post('/register', [
            'name' => '山田太郎',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);


        $user = User::where('email', 'test@example.com')->first();


        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );
    }

    public function test_verification_prompt_page_redirects_to_verification_url()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);


        $response = $this->actingAs($user)
            ->get('/email/verify');


        $response->assertStatus(200);

        $response->assertSee('認証はこちらから');
    }

    public function test_after_email_verification_redirects_to_attendance_page()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);


        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );


        $response = $this->actingAs($user)
            ->get($verificationUrl);


        $response->assertRedirect('/attendance?verified=1');


        $this->assertNotNull(
            $user->fresh()->email_verified_at
        );
    }
}
