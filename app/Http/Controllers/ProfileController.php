<?php

namespace App\Http\Controllers;

use App\Models\SalesAgent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Profile page and password change. Shared by the admin and agent panels;
 * $request->user() is whichever account the route's auth guard signed in.
 */
class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $account = $request->user();

        return view('profile', [
            'name' => $account instanceof SalesAgent ? $account->employee_name : $account->name,
            'email' => $account->email,
            'passwordRoute' => $account instanceof SalesAgent ? route('agent.profile.password') : route('profile.password'),
        ]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.current_password' => 'Current password is incorrect.',
            'new_password.min' => 'New password must be at least 6 characters long.',
            'new_password.confirmed' => 'New password and confirmation do not match.',
        ]);

        $request->user()->forceFill(['password' => $validated['new_password']])->save();

        return back()->with('notice', 'Password updated successfully.');
    }
}
