<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\SalesAgent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgentLeadTest extends TestCase
{
    use RefreshDatabase;

    private SalesAgent $agent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->agent = SalesAgent::factory()->create();
        $this->actingAs($this->agent, 'agent');
    }

    public function test_agent_only_sees_their_own_leads(): void
    {
        Lead::factory()->count(2)->create(['assigned_agent_id' => $this->agent->id]);
        Lead::factory()->create(['assigned_agent_id' => SalesAgent::factory()->create()->id]);
        Lead::factory()->create();

        $this->getJson(route('agent.leads.data'))
            ->assertOk()
            ->assertJsonPath('pagination.total', 2);
    }

    public function test_updating_status_clears_new_flag_and_logs_the_change(): void
    {
        $lead = Lead::factory()->create(['assigned_agent_id' => $this->agent->id, 'status' => 'In Process']);

        $this->patchJson(route('agent.leads.status', $lead), ['status' => 'Lost'])
            ->assertOk()
            ->assertJson(['success' => true, 'status' => 'Lost']);

        $this->assertSame('Lost', $lead->fresh()->status);
        $this->assertFalse($lead->fresh()->is_new);
        $this->assertDatabaseHas('lead_activity_logs', [
            'lead_id' => $lead->id, 'performed_by' => 'Agent', 'agent_id' => $this->agent->id,
            'old_value' => 'In Process', 'new_value' => 'Lost',
        ]);
    }

    public function test_saving_the_same_status_only_clears_new_flag(): void
    {
        $lead = Lead::factory()->create(['assigned_agent_id' => $this->agent->id, 'status' => 'In Process']);

        $this->patchJson(route('agent.leads.status', $lead), ['status' => 'In Process'])->assertOk();

        $this->assertFalse($lead->fresh()->is_new);
        $this->assertDatabaseCount('lead_activity_logs', 0);
    }

    public function test_agent_cannot_touch_someone_elses_lead(): void
    {
        $lead = Lead::factory()->create(['assigned_agent_id' => SalesAgent::factory()->create()->id]);

        $this->patchJson(route('agent.leads.status', $lead), ['status' => 'Won'])->assertForbidden();
        $this->getJson(route('agent.leads.notes.index', $lead))->assertForbidden();
        $this->postJson(route('agent.leads.notes.store', $lead), ['note' => 'hi'])->assertForbidden();

        $this->assertSame('In Process', $lead->fresh()->status);
    }

    public function test_agent_can_add_and_read_notes_on_their_lead(): void
    {
        $lead = Lead::factory()->create(['assigned_agent_id' => $this->agent->id]);

        $this->postJson(route('agent.leads.notes.store', $lead), ['note' => 'Follow up Monday'])->assertOk();

        $this->assertDatabaseHas('lead_notes', ['lead_id' => $lead->id, 'added_by' => 'Agent', 'agent_id' => $this->agent->id, 'admin_id' => null]);

        $this->getJson(route('agent.leads.notes.index', $lead))
            ->assertJsonPath('notes.0.author', $this->agent->employee_name);
    }

    public function test_agent_cannot_delete_notes(): void
    {
        $lead = Lead::factory()->create(['assigned_agent_id' => $this->agent->id]);
        $note = $lead->notes()->create(['note' => 'x', 'added_by' => 'Agent', 'agent_id' => $this->agent->id]);

        $this->deleteJson(route('notes.destroy', $note))->assertUnauthorized();

        $this->assertModelExists($note);
    }
}
