<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AgentRequest;
use App\Models\SalesAgent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Sales agents. Deleting an agent leaves their leads unassigned (foreign key ON DELETE SET NULL).
 */
class AgentController extends Controller
{
    public function index(): View
    {
        return view('admin.agents');
    }

    public function data(Request $request): JsonResponse
    {
        $query = SalesAgent::query()
            ->when($request->filled('employee_name'), fn ($query) => $query->whereLike('employee_name', '%'.$request->string('employee_name')->trim().'%'))
            ->when($request->filled('phone'), fn ($query) => $query->whereLike('phone', '%'.$request->string('phone')->trim().'%'))
            ->when($request->filled('email'), fn ($query) => $query->whereLike('email', '%'.$request->string('email')->trim().'%'))
            ->when(in_array($request->input('status'), ['Active', 'Inactive'], true), fn ($query) => $query->where('status', $request->input('status')))
            ->orderByDesc('id');

        $agents = $this->paginateWithinRange($query, $request);

        return response()->json([
            'success' => true,
            'agents' => $agents->getCollection()->map(fn (SalesAgent $agent): array => [
                'id' => $agent->id,
                'employee_name' => $agent->employee_name,
                'phone' => $agent->phone,
                'email' => $agent->email,
                'status' => $agent->status,
                'created_at' => $agent->created_at?->format('Y-m-d'),
            ]),
            'pagination' => $this->paginationMeta($agents),
        ]);
    }

    public function store(AgentRequest $request): JsonResponse
    {
        $agent = SalesAgent::create($request->validated());

        return response()->json(['success' => true, 'id' => $agent->id]);
    }

    public function update(AgentRequest $request, SalesAgent $agent): JsonResponse
    {
        $agent->update($request->safe()->only(['employee_name', 'phone', 'email', 'status']));

        return response()->json(['success' => true]);
    }

    public function destroy(SalesAgent $agent): JsonResponse
    {
        $agent->delete();

        return response()->json(['success' => true]);
    }
}
