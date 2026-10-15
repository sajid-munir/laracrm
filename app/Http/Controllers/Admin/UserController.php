<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Admin accounts.
 */
class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users');
    }

    public function data(Request $request): JsonResponse
    {
        $query = User::query()
            ->when($request->filled('name'), fn ($query) => $query->whereLike('name', '%'.$request->string('name')->trim().'%'))
            ->when($request->filled('email'), fn ($query) => $query->whereLike('email', '%'.$request->string('email')->trim().'%'))
            ->when(in_array($request->input('status'), ['Active', 'Inactive'], true), fn ($query) => $query->where('status', $request->input('status')))
            ->orderByDesc('id');

        $users = $this->paginateWithinRange($query, $request);
        $currentId = $request->user()->id;

        return response()->json([
            'success' => true,
            'users' => $users->getCollection()->map(fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
                'created_at' => $user->created_at?->format('Y-m-d'),
                'is_self' => $user->id === $currentId,
            ]),
            'pagination' => $this->paginationMeta($users),
        ]);
    }

    public function store(UserRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        return response()->json(['success' => true, 'id' => $user->id]);
    }

    public function update(UserRequest $request, User $user): JsonResponse
    {
        $user->update($request->safe()->only(['name', 'email', 'status']));

        return response()->json(['success' => true]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        if ($user->is($request->user())) {
            return response()->json(['message' => 'You cannot delete your own account.'], 422);
        }

        $user->delete();

        return response()->json(['success' => true]);
    }
}
