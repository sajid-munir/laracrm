<?php

namespace Tests\Feature;

use App\Mail\PasswordResetLinkMail;
use App\Models\PasswordReset;
use App\Models\SalesAgent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_email_gets_the_same_answer_and_no_mail(): void
    {
        Mail::fake();

        $this->post(route('password.email'), ['email' => 'nobody@example.com'])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status', 'If an account exists for that email, a password reset link has been sent to it.');

        Mail::assertNothingSent();
        $this->assertDatabaseCount('password_resets', 0);
    }

    public function test_reset_link_is_emailed_and_not_shown_on_the_page(): void
    {
        Mail::fake();
        User::factory()->create(['email' => 'admin@example.com']);

        $this->post(route('password.email'), ['email' => 'admin@example.com'])
            ->assertSessionHas('status', 'If an account exists for that email, a password reset link has been sent to it.')
            ->assertSessionMissing('reset_link');

        Mail::assertSent(PasswordResetLinkMail::class, fn (PasswordResetLinkMail $mail): bool => $mail->hasTo('admin@example.com'));
    }

    /**
     * Ask for a reset link and return the link from the email that was sent.
     */
    private function requestResetLink(string $email): string
    {
        Mail::fake();

        $this->post(route('password.email'), ['email' => $email]);

        $link = null;
        Mail::assertSent(PasswordResetLinkMail::class, function (PasswordResetLinkMail $mail) use (&$link): bool {
            $link = $mail->resetUrl;

            return true;
        });

        return $link;
    }

    public function test_agent_can_reset_their_password_with_the_generated_link(): void
    {
        $agent = SalesAgent::factory()->create(['email' => 'agent@example.com']);

        $link = $this->requestResetLink('agent@example.com');
        $token = basename(parse_url($link, PHP_URL_PATH));

        $this->assertDatabaseHas('password_resets', ['role' => 'Agent', 'email' => 'agent@example.com', 'token' => hash('sha256', $token)]);

        $this->get($link)->assertOk()->assertSee('Choose a new password');

        $this->post(route('password.update'), [
            'token' => $token,
            'password' => 'new-secret',
            'password_confirmation' => 'new-secret',
        ])->assertOk()->assertSee('reset successfully');

        $this->assertTrue(Hash::check('new-secret', $agent->fresh()->password));
        $this->assertTrue(PasswordReset::query()->firstOrFail()->used);
    }

    public function test_a_link_cannot_be_used_twice(): void
    {
        User::factory()->create(['email' => 'admin@example.com']);

        $link = $this->requestResetLink('admin@example.com');
        $token = basename(parse_url($link, PHP_URL_PATH));

        $payload = ['token' => $token, 'password' => 'new-secret', 'password_confirmation' => 'new-secret'];
        $this->post(route('password.update'), $payload)->assertSee('reset successfully');

        $this->get($link)->assertSee('invalid or has expired');
    }

    public function test_expired_links_are_rejected(): void
    {
        PasswordReset::create([
            'role' => 'Admin',
            'email' => 'admin@example.com',
            'token' => hash('sha256', 'old-token'),
            'expires_at' => now()->subMinute(),
        ]);

        $this->get(route('password.reset', 'old-token'))->assertSee('invalid or has expired');
    }
}
