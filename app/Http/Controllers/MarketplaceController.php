<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ProviderService;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class MarketplaceController extends Controller
{
    public function index(Request $request)
    {
        $categories = \App\Models\ServiceCategory::all();
        $query = Service::query();

        if ($request->filled('category')) {
            $query->whereHas('serviceCategory', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhere('subtasks', 'like', "%$search%")
                  ->orWhereHas('serviceCategory', function($sq) use ($search) {
                      $sq->where('name', 'like', "%$search%");
                  });
            });
        }

        $services = $query->get();
        return view('client.explore.index', compact('categories', 'services'));
    }

    public function show($id)
    {
        $service = Service::findOrFail($id);
        $providers = ProviderService::where('service_id', $id)
            ->with(['provider.providerProfile'])
            ->orderBy('price', 'asc')
            ->get();

        $user = auth()->user();
        $providerService = null;
        $clientCount = 0;

        if ($user && $user->role === 'provider') {
            $providerService = ProviderService::where('service_id', $id)
                ->where('provider_id', $user->id)
                ->first();
            
            if ($providerService) {
                $clientCount = \App\Models\Project::where('provider_service_id', $providerService->id)->count();
            }
        }
            
        return view('client.explore.show', compact('service', 'providers', 'providerService', 'clientCount'));
    }

    public function preChat($serviceId, $providerId)
    {
        $service = Service::findOrFail($serviceId);
        $provider = User::with('providerProfile')->findOrFail($providerId);
        $ps = ProviderService::where('service_id', $serviceId)->where('provider_id', $providerId)->firstOrFail();

        return view('client.explore.pre_chat', compact('service', 'provider', 'ps'));
    }

    public function portfolio()
    {
        $companies = Auth::user()->companies()->withCount('projects')->get();
        return view('client.portfolio', compact('companies'));
    }

    public function storeCompany(Request $request)
    {
        $user = Auth::user();
        if ($user->plan && $user->companies()->count() >= $user->plan->max_companies) {
            return redirect()->back()->withErrors(['error' => 'You have reached the maximum number of companies allowed by your plan.']);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:100',
            'about' => 'nullable|string',
        ]);

        $user->companies()->create($data);
        return redirect()->back()->with('success', 'Company added successfully.');
    }

    public function showCompany($id)
    {
        $company = Auth::user()->companies()->with(['projects.service', 'projects.provider'])->findOrFail($id);
        // Getting active users attached could be via a 'company_user' table, but for now we'll mock users if none exist
        return view('client.company_show', compact('company'));
    }

    public function updateCompany(Request $request, $id)
    {
        $company = Auth::user()->companies()->findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:100',
            'about' => 'nullable|string',
        ]);
        $company->update($data);
        return redirect()->back()->with('success', 'Company updated successfully.');
    }

    public function destroyCompany($id)
    {
        Auth::user()->companies()->findOrFail($id)->delete();
        return redirect()->route('client.portfolio')->with('success', 'Company deleted successfully.');
    }
}
