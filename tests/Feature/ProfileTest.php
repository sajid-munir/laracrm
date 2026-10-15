<?php

namespace Tests\Feature;

use App\Models\SalesAgent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_change_their_password(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin, 'admin')->get(route('profile'))->assertOk()->assertSee($admin->email);

        $this->actingAs($admin, 'admin')
            ->from(route('profile'))
            ->put(route('profile.password'), [
                'current_password' => 'password',
                'new_password' => 'new-secret',
                'new_password_confirmation' => 'new-secret',
            ])
            ->assertRedirect(route('profile'))
            ->assertSessionHas('notice', 'Password updated successfully.');

        $this->assertTrue(Hash::check('new-secret', $admin->fresh()->password));
    }

    public function test_agent_can_change_their_password(): void
    {
        $agent = SalesAgent::factory()->create();

        $this->actingAs($agent, 'agent')->get(route('agent.profile'))->assertOk()->assertSee($agent->employee_name);

        $this->actingAs($agent, 'agent')
            ->put(route('agent.profile.password'), [
                'current_password' => 'password',
                'new_password' => 'new-secret',
                'new_password_confirmation' => 'new-secret',
            ])
            ->assertSessionHas('notice');

        $this->assertTrue(Hash::check('new-secret', $agent->fresh()->password));
    }

    public function test_wrong_current_password_is_rejected(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin, 'admin')
            ->put(route('profile.password'), [
                'current_password' => 'wrong',
                'new_password' => 'new-secret',
                'new_password_confirmation' => 'new-secret',
            ])
            ->assertSessionHasErrors(['current_password' => 'Current password is incorrect.']);

        $this->assertTrue(Hash::check('password', $admin->fresh()->password));
    }
}
