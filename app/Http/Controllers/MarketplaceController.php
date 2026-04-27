<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ProviderService;
use App\Models\User;

class MarketplaceController extends Controller
{
    public function index()
    {
        $categories = Service::select('category')->distinct()->get();
        $services = Service::all();
        return view('client.explore.index', compact('categories', 'services'));
    }

    public function show($id)
    {
        $service = Service::findOrFail($id);
        $providers = ProviderService::where('service_id', $id)
            ->with(['provider.providerProfile'])
            ->orderBy('price', 'asc')
            ->get();
            
        return view('client.explore.show', compact('service', 'providers'));
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
        return view('client.portfolio');
    }
}
