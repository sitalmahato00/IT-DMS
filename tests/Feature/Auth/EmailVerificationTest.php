<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verify_email_screen_can_be_rendered(): void
    {
        $user = $this->makeUser(verified: false);

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertOk();
    }

    public function test_email_can_be_verified(): void
    {
        $user = $this->makeUser(verified: false);

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect($user->getDashboardRoute() . '?verified=1');
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $user = $this->makeUser(verified: false);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1('wrong@example.com'),
            ]
        );

        $this->actingAs($user)->get($verificationUrl)->assertForbidden();
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    private function makeUser(bool $verified): User
    {
        $user = User::factory()->create();
        $user->forceFill([
            'role' => 'student',
            'email_verified_at' => $verified ? now() : null,
        ])->save();

        return $user;
    }
}
