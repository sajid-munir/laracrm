<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\SalesAgent;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $statusCounts = array_fill_keys(Lead::STATUSES, 0);

        $countsByStatus = Lead::query()
            ->selectRaw('status, COUNT(*) AS total')
            ->groupBy('status')
            ->pluck('total', 'status');

        foreach ($countsByStatus as $status => $total) {
            $statusCounts[$status] = (int) $total;
        }

        return view('admin.dashboard', [
            'totalUsers' => User::query()->count(),
            'totalAgents' => SalesAgent::query()->count(),
            'totalLeads' => Lead::query()->count(),
            'totalNotes' => LeadNote::query()->count(),
            'statusCounts' => $statusCounts,
            'recentLeads' => Lead::query()->with('agent')->latest()->latest('id')->limit(6)->get(),
        ]);
    }
}
