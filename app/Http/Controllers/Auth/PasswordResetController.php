<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetLinkMail;
use App\Models\PasswordReset;
use App\Models\SalesAgent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

/**
 * Password reset for admins and agents. The one-time reset link is emailed to the
 * account's address; it is never shown on the page.
 */
class PasswordResetController extends Controller
{
    public function showForgot(): View
    {
        return view('auth.forgot-password');
    }

    public function sendLink(Request $request): RedirectResponse
    {
        $email = $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Please enter your email address.',
        ])['email'];

        $role = match (true) {
            User::query()->where('email', $email)->exists() => 'Admin',
            SalesAgent::query()->where('email', $email)->exists() => 'Agent',
            default => null,
        };

        if ($role !== null) {
            $plainToken = Str::random(64);

            PasswordReset::create([
                'role' => $role,
                'email' => $email,
                'token' => hash('sha256', $plainToken),
                'expires_at' => now()->addHour(),
            ]);

            try {
                Mail::to($email)->send(new PasswordResetLinkMail(route('password.reset', $plainToken)));
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        // Same answer whether or not the email exists, so the form cannot be used to find accounts.
        return back()->with('status', 'If an account exists for that email, a password reset link has been sent to it.');
    }

    public function showReset(string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'isValid' => PasswordReset::findValid($token) !== null,
        ]);
    }

    public function reset(Request $request): RedirectResponse|View
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'password.min' => 'Password must be at least 6 characters long.',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        $reset = PasswordReset::findValid($validated['token']);

        if ($reset === null) {
            return view('auth.reset-password', ['token' => $validated['token'], 'isValid' => false]);
        }

        DB::transaction(function () use ($reset, $validated): void {
            $account = $reset->accountQuery()->first();
            $account?->forceFill(['password' => $validated['password']])->save();

            $reset->update(['used' => true]);
        });

        return view('auth.reset-password', ['token' => '', 'isValid' => true, 'success' => true]);
    }
}
