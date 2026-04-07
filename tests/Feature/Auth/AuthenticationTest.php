<?php

namespace Tests\Feature\Auth;

use App\Models\ErpSetting;
use App\Models\User;
use App\Notifications\TwoFactorCodeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = $this->makeUser('student');

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect($user->getDashboardRoute());
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = $this->makeUser('student');

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = $this->makeUser('student');

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_two_factor_resend_is_rate_limited_for_thirty_seconds(): void
    {
        Notification::fake();
        Carbon::setTestNow('2026-04-07 12:00:00');

        try {
            $user = $this->makeUser('student');

            ErpSetting::set('security_two_factor_enabled', true, 'security', 'boolean');
            ErpSetting::set('security_two_factor_roles', ['student'], 'security', 'json');
            ErpSetting::set('security_two_factor_expiry_minutes', 10, 'security', 'integer');

            $loginResponse = $this->post('/login', [
                'email' => $user->email,
                'password' => 'password',
            ]);

            $loginResponse->assertRedirect(route('two-factor.challenge'));
            $loginResponse->assertSessionHas('two_factor.pending_user_id', $user->id);
            $this->assertGuest();

            $challengeResponse = $this->get('/two-factor-challenge');
            $challengeResponse->assertOk();
            $challengeResponse->assertSee('You can resend a new code in 30 seconds.');

            $resendResponse = $this->post('/two-factor-challenge/resend');

            $resendResponse->assertSessionHasErrors([
                'code' => 'Please wait 30 seconds before requesting another code.',
            ]);

            $this->assertCount(1, Notification::sent($user, TwoFactorCodeNotification::class));
        } finally {
            Carbon::setTestNow();
        }
    }

    private function makeUser(string $role): User
    {
        $user = User::factory()->create();
        $user->forceFill([
            'role' => $role,
            'email_verified_at' => now(),
        ])->save();

        return $user;
    }
}
