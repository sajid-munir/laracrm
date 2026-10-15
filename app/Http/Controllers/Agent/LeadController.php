<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * "My Leads" in the agent panel: only leads assigned to the signed-in agent.
 */
class LeadController extends Controller
{
    public function index(): View
    {
        return view('agent.leads');
    }

    public function data(Request $request): JsonResponse
    {
        $query = $request->user()->leads()->getQuery()
            ->when($request->filled('lead_title'), fn ($query) => $query->whereLike('lead_title', '%'.$request->string('lead_title')->trim().'%'))
            ->when($request->filled('customer_name'), fn ($query) => $query->whereLike('customer_name', '%'.$request->string('customer_name')->trim().'%'))
            ->when(in_array($request->input('status'), Lead::STATUSES, true), fn ($query) => $query->where('status', $request->input('status')))
            ->latest()
            ->latest('id');

        $leads = $this->paginateWithinRange($query, $request);

        return response()->json([
            'success' => true,
            'leads' => $leads->getCollection()->map(fn (Lead $lead): array => $lead->toListArray()),
            'pagination' => $this->paginationMeta($leads),
        ]);
    }

    /**
     * Change the lead's status. Any update by the agent also clears the "new" highlight.
     */
    public function updateStatus(Request $request, Lead $lead): JsonResponse
    {
        $agent = $request->user();

        abort_unless($lead->isVisibleTo($agent), 403, 'That lead is not assigned to you.');

        $status = $request->validate([
            'status' => ['required', Rule::in(Lead::STATUSES)],
        ], [
            'status.in' => 'Invalid status.',
        ])['status'];

        DB::transaction(function () use ($lead, $status, $agent): void {
            $oldStatus = $lead->status;

            $lead->update(['status' => $status, 'is_new' => false]);

            if ($status !== $oldStatus) {
                $lead->activityLogs()->create([
                    'action' => 'Status Update',
                    'old_value' => $oldStatus,
                    'new_value' => $status,
                    'performed_by' => 'Agent',
                    'agent_id' => $agent->id,
                ]);
            }
        });

        return response()->json(['success' => true, 'status' => $status]);
    }
}
