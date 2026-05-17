<?php

namespace Modules\ClientAPI\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ProviderService;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function services()
    {
        $services = Service::all()->map(function ($service) {
            return [
                'id' => $service->id,
                'name' => $service->getTranslatedName(),
                'original_name' => $service->name,
                'name_ar' => $service->name_ar,
                'slug' => $service->slug,
                'icon' => $service->icon,
                'description' => $service->description,
            ];
        });

        return response()->json($services);
    }

    public function providers($id)
    {
        $providers = ProviderService::where('service_id', $id)
            ->where('is_active', true)
            ->with(['provider.providerProfile'])
            ->get()
            ->map(function ($ps) {
                return [
                    'id' => $ps->id,
                    'provider_id' => $ps->provider_id,
                    'company_name' => $ps->provider->providerProfile->company_name ?? $ps->provider->name,
                    'monthly_price' => $ps->monthly_price,
                    'annual_price' => $ps->annual_price,
                    'annual_per_month' => $ps->annual_price ? ($ps->annual_price / 12) : null,
                    'discount_percentage' => $ps->annual_discount_percentage,
                    'delivery_days' => $ps->delivery_time_days,
                ];
            });

        return response()->json($providers);
    }
}
