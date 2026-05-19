<?php

namespace Modules\ClientAPI\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ProviderService;
use App\Traits\HandlesApiResponses;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    use HandlesApiResponses;

    public function services()
    {
        try {
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

            return $this->successResponse($services);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function providers($id)
    {
        try {
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

            return $this->successResponse($providers);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }
}
