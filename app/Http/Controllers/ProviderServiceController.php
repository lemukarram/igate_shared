<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ProviderService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProviderServiceController extends Controller
{
    public function index()
    {
        $allServices = Service::all();
        $myServices = ProviderService::where('provider_id', Auth::id())->with('service')->get();
        return view('provider.services.index', compact('allServices', 'myServices'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'price' => 'required|numeric|min:0',
            'delivery_time_days' => 'required|integer|min:1',
            'provider_notes' => 'nullable|string',
        ]);

        $existing = ProviderService::where('provider_id', $user->id)
            ->where('service_id', $validated['service_id'])
            ->exists();
            
        if (!$existing && $user->plan && $user->providerServices()->count() >= $user->plan->max_services) {
            return redirect()->back()->withErrors(['error' => 'You have reached the maximum number of services allowed by your plan.']);
        }

        ProviderService::updateOrCreate(
            [
                'provider_id' => $user->id,
                'service_id' => $validated['service_id'],
            ],
            $validated
        );

        return redirect()->back()->with('success', 'Service offering updated successfully.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
            'delivery_time_days' => 'required|integer|min:1',
            'provider_notes' => 'nullable|string',
        ]);

        $ps = ProviderService::where('provider_id', \Illuminate\Support\Facades\Auth::id())->findOrFail($id);
        $ps->update($validated);

        return redirect()->back()->with('success', 'Service details updated successfully.');
    }

    public function clients()
    {
        $clients = \App\Models\Project::where('provider_id', Auth::id())
            ->with(['client', 'service'])
            ->get()
            ->groupBy('client_id');
            
        return view('provider.clients.index', compact('clients'));
    }

    public function clientShow($id)
    {
        $client = User::findOrFail($id);
        $projects = \App\Models\Project::where('provider_id', Auth::id())
            ->where('client_id', $id)
            ->with('service')
            ->get();
        
        $activities = \App\Models\ClientActivity::where('client_id', $id)
            ->where('provider_id', Auth::id())
            ->latest()
            ->take(10)
            ->get();
            
        return view('provider.clients.show', compact('client', 'projects', 'activities'));
    }

    public function storeReleaseRequest(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        $project = \App\Models\Project::findOrFail($validated['project_id']);
        
        if ($project->provider_id !== Auth::id()) {
            abort(403);
        }

        \App\Models\ReleaseRequest::create([
            'project_id' => $project->id,
            'provider_id' => Auth::id(),
            'amount' => $validated['amount'],
            'notes' => $validated['notes'],
            'status' => 'pending',
        ]);

        // Record activity
        \App\Models\ClientActivity::create([
            'client_id' => $project->client_id,
            'provider_id' => Auth::id(),
            'project_id' => $project->id,
            'activity_type' => 'release_requested',
            'description' => 'Provider requested a payment release of ' . number_format($validated['amount'], 2) . ' SAR.',
        ]);

        return redirect()->back()->with('success', 'Release request sent successfully.');
    }

    public function teamTasks()
    {
        return view('provider.team_tasks');
    }

    public function destroy($id)
    {
        ProviderService::where('provider_id', Auth::id())->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Service removed.');
    }
}
