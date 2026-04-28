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
            
        return view('provider.clients.show', compact('client', 'projects'));
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
