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

    public function services(Request $request)
    {
        try {
            $services = Service::query()
                ->when($request->has('category_id'), function ($query) use ($request) {
                    $query->where('service_category_id', $request->category_id);
                })
                ->where('is_active', true)
                ->get()
                ->map(function ($service) use ($request) {
                    $data = [
                        'id' => $service->id,
                        'name' => $service->getTranslatedName(),
                        'original_name' => $service->name,
                        'name_ar' => $service->name_ar,
                        'slug' => $service->slug,
                        'icon' => $service->icon,
                        'description' => $service->description,
                        'service_category_id' => $service->service_category_id,
                    ];

                    if ($request->boolean('include_subtasks')) {
                        $data['subtasks'] = $service->subtasks;
                    }

                    return $data;
                });

            return $this->successResponse($services);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function categories()
    {
        try {
            $categories = \App\Models\ServiceCategory::all();
            return $this->successResponse($categories);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function categoryDetail($id)
    {
        try {
            $category = \App\Models\ServiceCategory::with(['services' => function($q) {
                $q->where('is_active', true);
            }])->findOrFail($id);
            
            return $this->successResponse($category);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function serviceDetail($id)
    {
        try {
            $service = Service::with(['serviceCategory'])->findOrFail($id);
            
            $data = [
                'id' => $service->id,
                'name' => $service->getTranslatedName(),
                'original_name' => $service->name,
                'name_ar' => $service->name_ar,
                'slug' => $service->slug,
                'icon' => $service->icon,
                'description' => $service->description,
                'subtasks' => $service->subtasks,
                'category' => $service->serviceCategory,
            ];

            return $this->successResponse($data);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function serviceProviders($id)
    {
        try {
            $service = Service::findOrFail($id);
            $providers = ProviderService::where('service_id', $id)
                ->where('is_active', true)
                ->with(['provider.providerProfile'])
                ->get()
                ->map(function ($ps) {
                    return [
                        'id' => $ps->id,
                        'provider_id' => $ps->provider_id,
                        'company_name' => $ps->provider->providerProfile->company_name ?? $ps->provider->name,
                        'logo' => $ps->provider->providerProfile->logo ? url('storage/' . $ps->provider->providerProfile->logo) : null,
                        'rating' => $ps->provider->providerProfile->rating ?? 0,
                        'monthly_price' => $ps->monthly_price,
                        'annual_price' => $ps->annual_price,
                        'annual_per_month' => $ps->annual_price ? ($ps->annual_price / 12) : null,
                        'discount_percentage' => $ps->annual_discount_percentage,
                        'delivery_days' => $ps->delivery_time_days,
                    ];
                });

            return $this->successResponse([
                'service' => [
                    'id' => $service->id,
                    'name' => $service->getTranslatedName(),
                    'subtasks' => $service->subtasks,
                ],
                'providers' => $providers
            ]);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function providerDetail($id)
    {
        try {
            $provider = \App\Models\User::role('provider')
                ->with(['providerProfile', 'providerServices.service'])
                ->findOrFail($id);

            $profile = $provider->providerProfile;
            
            return $this->successResponse([
                'id' => $provider->id,
                'name' => $provider->name,
                'email' => $provider->email,
                'profile' => [
                    'company_name' => $profile->company_name,
                    'bio' => $profile->bio,
                    'logo' => $profile->logo ? url('storage/' . $profile->logo) : null,
                    'rating' => $profile->rating ?? 0,
                    'completed_projects' => $profile->projects()->where('status', 'completed')->count(),
                ],
                'services' => $provider->providerServices->map(function($ps) {
                    return [
                        'id' => $ps->id,
                        'service_id' => $ps->service_id,
                        'service_name' => $ps->service->getTranslatedName(),
                        'monthly_price' => $ps->monthly_price,
                        'annual_price' => $ps->annual_price,
                    ];
                })
            ]);
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
