<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Payment;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProviderDashboardController extends Controller
{
    public function index()
    {
        $providerId = Auth::id();

        // 1. Total Revenue (Completed projects/payments)
        $totalRevenue = Payment::whereHas('project', function($query) use ($providerId) {
            $query->where('provider_id', $providerId);
        })->where('status', 'completed')->sum('amount');

        // 2. Active Clients (Distinct clients with active projects)
        $activeClientsCount = Project::where('provider_id', $providerId)
            ->where('status', 'active')
            ->distinct('client_id')
            ->count('client_id');

        // 3. SLA Compliance (Projects completed before or on end_date)
        $completedProjects = Project::where('provider_id', $providerId)
            ->where('status', 'completed')
            ->get();
        
        $slaCompliant = $completedProjects->count() > 0 
            ? ($completedProjects->filter(function($p) {
                return $p->completed_at <= $p->end_date;
              })->count() / $completedProjects->count()) * 100 
            : 100;

        // 4. Pending Tasks
        $pendingTasksCount = Task::whereHas('project', function($query) use ($providerId) {
            $query->where('provider_id', $providerId);
        })->whereIn('status', ['todo', 'in_progress'])->count();

        // Monthly Revenue Growth (Compare this month with last month)
        $thisMonthRevenue = Payment::whereHas('project', function($query) use ($providerId) {
            $query->where('provider_id', $providerId);
        })->where('status', 'completed')
          ->whereMonth('created_at', Carbon::now()->month)
          ->sum('amount');

        $lastMonthRevenue = Payment::whereHas('project', function($query) use ($providerId) {
            $query->where('provider_id', $providerId);
        })->where('status', 'completed')
          ->whereMonth('created_at', Carbon::now()->subMonth()->month)
          ->sum('amount');

        $growth = $lastMonthRevenue > 0 
            ? (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 
            : ($thisMonthRevenue > 0 ? 100 : 0);

        // Recent Active Projects
        $recentProjects = Project::where('provider_id', $providerId)
            ->where('status', 'active')
            ->with(['service', 'client'])
            ->latest()
            ->take(5)
            ->get();

        // Recent Pre-sale Chats
        $recentChats = \App\Models\PreSaleMessage::where('provider_id', $providerId)
            ->with(['service', 'client'])
            ->latest()
            ->get()
            ->unique(function ($item) {
                return $item->client_id . '-' . $item->service_id;
            })
            ->take(5);

        return view('provider.dashboard', compact(
            'totalRevenue', 
            'activeClientsCount', 
            'slaCompliant', 
            'pendingTasksCount',
            'growth',
            'recentProjects',
            'recentChats'
        ));
    }
}
