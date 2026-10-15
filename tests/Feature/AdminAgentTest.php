<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\SalesAgent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAgentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(), 'admin');
    }

    public function test_agents_page_renders(): void
    {
        $this->get(route('agents.index'))->assertOk()->assertSee('All Agents');
    }

    public function test_list_is_paginated_ten_per_page(): void
    {
        SalesAgent::factory()->count(12)->create();

        $this->getJson(route('agents.data', ['page' => 2]))
            ->assertOk()
            ->assertJsonCount(2, 'agents')
            ->assertJsonPath('pagination.total', 12)
            ->assertJsonPath('pagination.total_pages', 2);
    }

    public function test_a_page_past_the_end_shows_the_last_page(): void
    {
        SalesAgent::factory()->count(3)->create();

        $this->getJson(route('agents.data', ['page' => 5]))
            ->assertJsonPath('pagination.page', 1)
            ->assertJsonCount(3, 'agents');
    }

    public function test_admin_can_add_update_and_delete_an_agent(): void
    {
        $this->postJson(route('agents.store'), [
            'employee_name' => 'Ali',
            'phone' => '03001234567',
            'email' => 'ali@example.com',
            'password' => 'secret1',
            'status' => 'Active',
        ])->assertOk();

        $agent = SalesAgent::query()->where('email', 'ali@example.com')->firstOrFail();

        $this->putJson(route('agents.update', $agent), [
            'employee_name' => 'Ali Raza',
            'phone' => '03001234567',
            'email' => 'ali@example.com',
            'status' => 'Inactive',
        ])->assertOk();

        $this->assertSame('Ali Raza', $agent->fresh()->employee_name);

        $this->deleteJson(route('agents.destroy', $agent))->assertOk();
        $this->assertModelMissing($agent);
    }

    public function test_deleting_an_agent_leaves_their_leads_unassigned(): void
    {
        $agent = SalesAgent::factory()->create();
        $lead = Lead::factory()->create(['assigned_agent_id' => $agent->id]);

        $this->deleteJson(route('agents.destroy', $agent))->assertOk();

        $this->assertNull($lead->fresh()->assigned_agent_id);
    }

    public function test_password_is_required_for_a_new_agent(): void
    {
        $this->postJson(route('agents.store'), [
            'employee_name' => 'Ali',
            'email' => 'ali@example.com',
            'status' => 'Active',
        ])->assertUnprocessable()->assertJsonValidationErrors('password');
    }
}
