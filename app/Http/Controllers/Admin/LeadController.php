<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeadRequest;
use App\Models\Lead;
use App\Models\SalesAgent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Leads in the admin panel: the All, Assigned and Unassigned pages share one view.
 */
class LeadController extends Controller
{
    private const TITLES = [
        'all' => 'All Leads',
        'assigned' => 'Assigned Leads',
        'unassigned' => 'Unassigned Leads',
    ];

    public function index(string $scope): View
    {
        return view('admin.leads', [
            'scope' => $scope,
            'title' => self::TITLES[$scope],
            'agents' => SalesAgent::query()->orderBy('employee_name')->get(['id', 'employee_name', 'status']),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $agentFilter = $request->string('assigned_agent_id')->trim()->toString();

        $query = Lead::query()
            ->with('agent')
            ->when($request->filled('lead_title'), fn ($query) => $query->whereLike('lead_title', '%'.$request->string('lead_title')->trim().'%'))
            ->when($request->filled('customer_name'), fn ($query) => $query->whereLike('customer_name', '%'.$request->string('customer_name')->trim().'%'))
            ->when($request->filled('phone'), fn ($query) => $query->whereLike('phone', '%'.$request->string('phone')->trim().'%'))
            ->when($request->filled('email'), fn ($query) => $query->whereLike('email', '%'.$request->string('email')->trim().'%'))
            ->when(in_array($request->input('status'), Lead::STATUSES, true), fn ($query) => $query->where('status', $request->input('status')))
            ->latest()
            ->latest('id');

        // "assignment" comes from the page (Assigned / Unassigned); "assigned_agent_id" from the
        // Assigned Agent filter, where "0" means unassigned.
        if ($request->input('assignment') === 'unassigned' || $agentFilter === '0') {
            $query->whereNull('assigned_agent_id');
        } elseif ($agentFilter !== '') {
            $query->where('assigned_agent_id', (int) $agentFilter);
        } elseif ($request->input('assignment') === 'assigned') {
            $query->whereNotNull('assigned_agent_id');
        }

        $leads = $this->paginateWithinRange($query, $request);

        return response()->json([
            'success' => true,
            'leads' => $leads->getCollection()->map(fn (Lead $lead): array => $lead->toListArray()),
            'pagination' => $this->paginationMeta($leads),
        ]);
    }

    public function store(LeadRequest $request): JsonResponse
    {
        $lead = Lead::create($request->validated());

        return response()->json(['success' => true, 'id' => $lead->id]);
    }

    /**
     * Save the lead, logging status changes and agent reassignments.
     */
    public function update(LeadRequest $request, Lead $lead): JsonResponse
    {
        $data = $request->validated();
        $adminId = $request->user()->id;

        DB::transaction(function () use ($lead, $data, $adminId): void {
            $oldStatus = $lead->status;
            $oldAgentId = $lead->assigned_agent_id;

            $lead->update($data);

            if ($lead->status !== $oldStatus) {
                $lead->activityLogs()->create([
                    'action' => 'Status Update',
                    'old_value' => $oldStatus,
                    'new_value' => $lead->status,
                    'performed_by' => 'Admin',
                    'admin_id' => $adminId,
                ]);
            }

            if ($lead->assigned_agent_id !== $oldAgentId) {
                $lead->activityLogs()->create([
                    'action' => 'Agent Reassigned',
                    'old_value' => (string) $oldAgentId,
                    'new_value' => (string) $lead->assigned_agent_id,
                    'performed_by' => 'Admin',
                    'admin_id' => $adminId,
                ]);
            }
        });

        return response()->json(['success' => true]);
    }

    public function destroy(Lead $lead): JsonResponse
    {
        $lead->delete();

        return response()->json(['success' => true]);
    }
}
