<?php

namespace Tests\Feature;

use App\Models\SalesAgent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_sent_to_the_login_page(): void
    {
        $this->get('/')->assertRedirect(route('login'));
        $this->get('/agent')->assertRedirect(route('login'));
    }

    public function test_login_page_renders(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Sign In');
    }

    public function test_admin_can_sign_in_and_lands_on_the_dashboard(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com']);

        $this->post(route('login'), ['email' => 'admin@example.com', 'password' => 'password'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($admin, 'admin');
        $this->get(route('dashboard'))->assertOk()->assertSee('Recent Leads');
    }

    public function test_agent_can_sign_in_and_lands_on_my_leads(): void
    {
        $agent = SalesAgent::factory()->create(['email' => 'agent@example.com']);

        $this->post(route('login'), ['email' => 'agent@example.com', 'password' => 'password'])
            ->assertRedirect(route('agent.leads'));

        $this->assertAuthenticatedAs($agent, 'agent');
        $this->get(route('agent.leads'))->assertOk()->assertSee('My Leads');
    }

    public function test_wrong_password_is_rejected(): void
    {
        User::factory()->create(['email' => 'admin@example.com']);

        $this->from(route('login'))
            ->post(route('login'), ['email' => 'admin@example.com', 'password' => 'wrong'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => 'Invalid email or password.']);

        $this->assertGuest('admin');
    }

    public function test_inactive_accounts_cannot_sign_in(): void
    {
        User::factory()->inactive()->create(['email' => 'admin@example.com']);
        SalesAgent::factory()->inactive()->create(['email' => 'agent@example.com']);

        $this->post(route('login'), ['email' => 'admin@example.com', 'password' => 'password'])
            ->assertSessionHasErrors(['email' => 'Your account is inactive. Please contact another administrator.']);

        $this->post(route('login'), ['email' => 'agent@example.com', 'password' => 'password'])
            ->assertSessionHasErrors(['email' => 'Your agent account is inactive. Please contact the administrator.']);

        $this->assertGuest('admin');
        $this->assertGuest('agent');
    }

    public function test_agent_cannot_open_admin_pages(): void
    {
        $agent = SalesAgent::factory()->create();

        $this->actingAs($agent, 'agent')->get(route('users.index'))->assertRedirect(route('login'));
        $this->actingAs($agent, 'agent')->getJson(route('leads.data'))->assertUnauthorized();
    }

    public function test_admin_cannot_open_the_agent_panel(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin, 'admin')->getJson(route('agent.leads.data'))->assertUnauthorized();
    }

    public function test_signed_in_user_visiting_login_goes_to_their_panel(): void
    {
        $this->actingAs(SalesAgent::factory()->create(), 'agent')
            ->get(route('login'))
            ->assertRedirect(route('agent.leads'));
    }

    public function test_logout_signs_out(): void
    {
        $this->actingAs(User::factory()->create(), 'admin')
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest('admin');
    }
}
