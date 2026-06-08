<?php

namespace Modules\ClientAPI\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Traits\HandlesApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    use HandlesApiResponses;

    public function index()
    {
        return $this->successResponse(Auth::user()->companies);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'industry' => 'nullable|string',
                'registration_number' => 'nullable|string',
                'about' => 'nullable|string',
            ]);

            $company = Company::create(array_merge($validated, [
                'client_id' => Auth::id(),
                'is_active' => true,
            ]));

            return $this->successResponse($company, 'Company created successfully', 201);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        try {
            $company = Auth::user()->companies()->with(['projects' => function($q) {
                $q->whereIn('status', ['active', 'pending_payment'])
                  ->with(['service', 'provider.providerProfile']);
            }])->findOrFail($id);

            $data = $company->toArray();
            
            // Format projects for cleaner mobile response
            $data['active_projects'] = $company->projects->map(function($project) {
                return [
                    'id' => $project->id,
                    'status' => $project->status,
                    'service_name' => $project->service->getTranslatedName(),
                    'provider_name' => $project->provider->providerProfile->company_name ?? $project->provider->name,
                    'start_date' => $project->start_date,
                    'total_amount' => $project->total_amount,
                ];
            });

            // Remove the raw projects relationship if we are providing a formatted one
            unset($data['projects']);

            return $this->successResponse($data);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $company = Auth::user()->companies()->findOrFail($id);
            
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'industry' => 'nullable|string',
                'registration_number' => 'nullable|string',
                'about' => 'nullable|string',
            ]);

            $company->update($validated);

            return $this->successResponse($company, 'Company updated successfully');
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($id)
    {
        try {
            $company = Auth::user()->companies()->findOrFail($id);
            $company->delete();

            return $this->successResponse(null, 'Company deleted successfully', 204);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }
}
