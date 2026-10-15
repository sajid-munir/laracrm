<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\SalesAgent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Notes on a lead. Shared by the admin and agent panels; agents can only
 * reach leads assigned to them, and only admins can delete notes.
 */
class LeadNoteController extends Controller
{
    public function index(Request $request, Lead $lead): JsonResponse
    {
        abort_unless($lead->isVisibleTo($request->user()), 403, 'That lead is not assigned to you.');

        $notes = $lead->notes()->with(['admin', 'agent'])->latest()->latest('id')->get();

        return response()->json([
            'success' => true,
            'notes' => $notes->map(fn (LeadNote $note): array => $note->toListArray()),
        ]);
    }

    public function store(Request $request, Lead $lead): JsonResponse
    {
        $account = $request->user();

        abort_unless($lead->isVisibleTo($account), 403, 'That lead is not assigned to you.');

        $validated = $request->validate([
            'note' => ['required', 'string'],
        ], [
            'note.required' => 'Note cannot be empty.',
        ]);

        $isAgent = $account instanceof SalesAgent;

        $lead->notes()->create([
            'note' => $validated['note'],
            'added_by' => $isAgent ? 'Agent' : 'Admin',
            'admin_id' => $isAgent ? null : $account->id,
            'agent_id' => $isAgent ? $account->id : null,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(LeadNote $note): JsonResponse
    {
        $note->delete();

        return response()->json(['success' => true]);
    }
}
