<?php

namespace Modules\ClientAPI\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function index()
    {
        return response()->json(Auth::user()->companies);
    }

    public function store(Request $request)
    {
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

        return response()->json($company, 201);
    }

    public function show($id)
    {
        $company = Auth::user()->companies()->findOrFail($id);
        return response()->json($company);
    }

    public function update(Request $request, $id)
    {
        $company = Auth::user()->companies()->findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'industry' => 'nullable|string',
            'registration_number' => 'nullable|string',
            'about' => 'nullable|string',
        ]);

        $company->update($validated);

        return response()->json($company);
    }

    public function destroy($id)
    {
        $company = Auth::user()->companies()->findOrFail($id);
        $company->delete();

        return response()->json(null, 204);
    }
}
