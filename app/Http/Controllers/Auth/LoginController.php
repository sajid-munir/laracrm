<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SalesAgent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * One login form for both admins and agents. The email is looked up in the
 * admin table first, then in the agent table.
 */
class LoginController extends Controller
{
    public function show(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Please enter both email and password.',
            'password.required' => 'Please enter both email and password.',
        ]);

        $admin = User::query()->where('email', $credentials['email'])->first();

        if ($admin && Hash::check($credentials['password'], $admin->password)) {
            if (! $admin->isActive()) {
                return $this->failed('Your account is inactive. Please contact another administrator.');
            }

            return $this->signIn($request, 'admin', $admin, route('dashboard'));
        }

        $agent = SalesAgent::query()->where('email', $credentials['email'])->first();

        if ($agent && Hash::check($credentials['password'], $agent->password)) {
            if (! $agent->isActive()) {
                return $this->failed('Your agent account is inactive. Please contact the administrator.');
            }

            return $this->signIn($request, 'agent', $agent, route('agent.leads'));
        }

        return $this->failed('Invalid email or password.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        Auth::guard('agent')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function signIn(Request $request, string $guard, User|SalesAgent $account, string $home): RedirectResponse
    {
        // Only one role may be signed in at a time.
        Auth::guard($guard === 'admin' ? 'agent' : 'admin')->logout();
        Auth::guard($guard)->login($account);

        $request->session()->regenerate();

        return redirect()->intended($home);
    }

    private function failed(string $message): RedirectResponse
    {
        return back()->withErrors(['email' => $message])->onlyInput('email');
    }
}
