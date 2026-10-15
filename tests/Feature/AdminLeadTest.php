<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\SalesAgent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLeadTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->actingAs($this->admin, 'admin');
    }

    public function test_all_assigned_and_unassigned_pages_render(): void
    {
        $this->get(route('leads.index'))->assertOk()->assertSee('All Leads');
        $this->get(route('leads.assigned'))->assertOk()->assertSee('Assigned Leads');
        $this->get(route('leads.unassigned'))->assertOk()->assertSee('Unassigned Leads')->assertDontSee('Assigned Agent</th>', false);
    }

    public function test_list_filters_by_assignment_agent_and_status(): void
    {
        $agent = SalesAgent::factory()->create();
        Lead::factory()->create(['assigned_agent_id' => $agent->id, 'status' => 'Won']);
        Lead::factory()->create(['assigned_agent_id' => $agent->id]);
        Lead::factory()->create();

        $this->getJson(route('leads.data'))->assertJsonPath('pagination.total', 3);
        $this->getJson(route('leads.data', ['assignment' => 'assigned']))->assertJsonPath('pagination.total', 2);
        $this->getJson(route('leads.data', ['assignment' => 'unassigned']))->assertJsonPath('pagination.total', 1);
        $this->getJson(route('leads.data', ['assigned_agent_id' => '0']))->assertJsonPath('pagination.total', 1);
        $this->getJson(route('leads.data', ['assigned_agent_id' => $agent->id, 'status' => 'Won']))
            ->assertJsonPath('pagination.total', 1)
            ->assertJsonPath('leads.0.agent_name', $agent->employee_name);
    }

    public function test_admin_can_add_a_lead(): void
    {
        $this->postJson(route('leads.store'), [
            'lead_title' => 'Website enquiry',
            'customer_name' => 'Sara',
            'status' => 'In Process',
            'assigned_agent_id' => '',
        ])->assertOk();

        $this->assertDatabaseHas('leads', ['lead_title' => 'Website enquiry', 'assigned_agent_id' => null, 'is_new' => true]);
    }

    public function test_invalid_status_and_missing_agent_are_rejected(): void
    {
        $this->postJson(route('leads.store'), [
            'lead_title' => 'X',
            'customer_name' => 'Y',
            'status' => 'Pending',
            'assigned_agent_id' => 999,
        ])->assertUnprocessable()->assertJsonValidationErrors([
            'status' => 'Invalid status.',
            'assigned_agent_id' => 'Selected agent does not exist.',
        ]);
    }

    public function test_updating_status_and_agent_writes_activity_logs(): void
    {
        $oldAgent = SalesAgent::factory()->create();
        $newAgent = SalesAgent::factory()->create();
        $lead = Lead::factory()->create(['assigned_agent_id' => $oldAgent->id, 'status' => 'In Process']);

        $this->putJson(route('leads.update', $lead), [
            'lead_title' => $lead->lead_title,
            'customer_name' => $lead->customer_name,
            'status' => 'Won',
            'assigned_agent_id' => $newAgent->id,
        ])->assertOk();

        $this->assertDatabaseHas('lead_activity_logs', [
            'lead_id' => $lead->id, 'action' => 'Status Update', 'old_value' => 'In Process', 'new_value' => 'Won',
            'performed_by' => 'Admin', 'admin_id' => $this->admin->id,
        ]);
        $this->assertDatabaseHas('lead_activity_logs', [
            'lead_id' => $lead->id, 'action' => 'Agent Reassigned',
            'old_value' => (string) $oldAgent->id, 'new_value' => (string) $newAgent->id,
        ]);
    }

    public function test_saving_without_changes_writes_no_logs(): void
    {
        $lead = Lead::factory()->create();

        $this->putJson(route('leads.update', $lead), [
            'lead_title' => 'New title',
            'customer_name' => $lead->customer_name,
            'status' => $lead->status,
        ])->assertOk();

        $this->assertDatabaseCount('lead_activity_logs', 0);
    }

    public function test_deleting_a_lead_removes_its_notes(): void
    {
        $lead = Lead::factory()->create();
        $lead->notes()->create(['note' => 'Called', 'added_by' => 'Admin', 'admin_id' => $this->admin->id]);

        $this->deleteJson(route('leads.destroy', $lead))->assertOk();

        $this->assertModelMissing($lead);
        $this->assertDatabaseCount('lead_notes', 0);
    }

    public function test_admin_can_add_list_and_delete_notes(): void
    {
        $lead = Lead::factory()->create();

        $this->postJson(route('leads.notes.store', $lead), ['note' => 'Called the customer'])->assertOk();

        $this->getJson(route('leads.notes.index', $lead))
            ->assertJsonPath('notes.0.note', 'Called the customer')
            ->assertJsonPath('notes.0.author', $this->admin->name);

        $note = LeadNote::query()->firstOrFail();
        $this->deleteJson(route('notes.destroy', $note))->assertOk();
        $this->assertModelMissing($note);
    }

    public function test_empty_note_is_rejected(): void
    {
        $lead = Lead::factory()->create();

        $this->postJson(route('leads.notes.store', $lead), ['note' => ''])
            ->assertJsonValidationErrors(['note' => 'Note cannot be empty.']);
    }
}
