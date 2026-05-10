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

    public function show($id, \App\Settings\GeneralSettings $settings)
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
            
        return view('client.explore.show', compact('service', 'providers', 'providerService', 'clientCount', 'settings'));
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

    public function myProviders()
    {
        $user = Auth::user();
        
        // Providers from Projects
        $projectProviderIds = \App\Models\Project::where('client_id', $user->id)
            ->pluck('provider_id')
            ->toArray();

        // Providers from PreSaleMessages
        $chatProviderIds = \App\Models\PreSaleMessage::where('client_id', $user->id)
            ->pluck('provider_id')
            ->toArray();
        
        $allProviderIds = array_unique(array_merge($projectProviderIds, $chatProviderIds));
        
        $providers = User::whereIn('id', $allProviderIds)
            ->with(['providerProfile', 'providerServices.service'])
            ->get();

        return view('client.my_providers', compact('providers'));
    }

    public function storeCompany(Request $request)
    {
        $user = Auth::user();
        if ($user->plan && $user->companies()->where('is_active', true)->count() >= $user->plan->max_companies) {
            return redirect()->route('settings.plan.upgrade')->with('error', 'You have reached the maximum number of active companies allowed by your plan.');
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
        
        $preSaleChats = \App\Models\PreSaleMessage::where('client_id', Auth::id())
            ->with(['service', 'provider.providerProfile'])
            ->get()
            ->groupBy(function($msg) {
                return $msg->service_id . '-' . $msg->provider_id;
            })
            ->map(function($group) {
                return $group->last();
            });

        return view('client.company_show', compact('company', 'preSaleChats'));
    }

    public function updateCompany(Request $request, $id)
    {
        $company = Auth::user()->companies()->findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:100',
            'about' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('companies/logos', 'public');
        }

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('companies/documents', 'public');
                Auth::user()->documents()->create([
                    'name' => $company->name . ' - ' . $file->getClientOriginalName(),
                    'file_path' => $path,
                    'type' => $file->getClientMimeType(),
                ]);
            }
        }

        $company->update($data);

        return redirect()->back()->with('success', 'Company updated successfully.');
    }

    public function destroyCompany($id)
    {
        Auth::user()->companies()->findOrFail($id)->delete();
        return redirect()->route('client.portfolio')->with('success', 'Company deleted successfully.');
    }
}
