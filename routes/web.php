<?php

use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Agent\LeadController as AgentLeadController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\LeadNoteController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
| Sign in, sign out and password reset (shared by admins and agents)
*/
Route::middleware('guest:admin,agent')->group(function () {
    Route::get('login', [LoginController::class, 'show'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->middleware('throttle:10,1');

    Route::get('forgot-password', [PasswordResetController::class, 'showForgot'])->name('password.request');
    Route::post('forgot-password', [PasswordResetController::class, 'sendLink'])->middleware('throttle:5,1')->name('password.email');
    Route::get('reset-password/{token}', [PasswordResetController::class, 'showReset'])->name('password.reset');
    Route::post('reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout');

/*
| Admin panel
*/
Route::middleware('auth:admin')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/data', [UserController::class, 'data'])->name('users.data');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('agents', [AgentController::class, 'index'])->name('agents.index');
    Route::get('agents/data', [AgentController::class, 'data'])->name('agents.data');
    Route::post('agents', [AgentController::class, 'store'])->name('agents.store');
    Route::put('agents/{agent}', [AgentController::class, 'update'])->name('agents.update');
    Route::delete('agents/{agent}', [AgentController::class, 'destroy'])->name('agents.destroy');

    Route::get('leads', [LeadController::class, 'index'])->defaults('scope', 'all')->name('leads.index');
    Route::get('leads/assigned', [LeadController::class, 'index'])->defaults('scope', 'assigned')->name('leads.assigned');
    Route::get('leads/unassigned', [LeadController::class, 'index'])->defaults('scope', 'unassigned')->name('leads.unassigned');
    Route::get('leads/data', [LeadController::class, 'data'])->name('leads.data');
    Route::post('leads', [LeadController::class, 'store'])->name('leads.store');
    Route::put('leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
    Route::delete('leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');

    Route::get('leads/{lead}/notes', [LeadNoteController::class, 'index'])->name('leads.notes.index');
    Route::post('leads/{lead}/notes', [LeadNoteController::class, 'store'])->name('leads.notes.store');
    Route::delete('notes/{note}', [LeadNoteController::class, 'destroy'])->name('notes.destroy');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

/*
| Agent panel
*/
Route::middleware('auth:agent')->prefix('agent')->name('agent.')->group(function () {
    Route::get('/', [AgentLeadController::class, 'index'])->name('leads');
    Route::get('leads/data', [AgentLeadController::class, 'data'])->name('leads.data');
    Route::patch('leads/{lead}/status', [AgentLeadController::class, 'updateStatus'])->name('leads.status');
    Route::get('leads/{lead}/notes', [LeadNoteController::class, 'index'])->name('leads.notes.index');
    Route::post('leads/{lead}/notes', [LeadNoteController::class, 'store'])->name('leads.notes.store');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});
