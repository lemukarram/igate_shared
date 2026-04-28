<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProviderService;
use App\Models\Project;
use App\Models\Payment;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function review($providerServiceId)
    {
        $ps = ProviderService::with(['service', 'provider.providerProfile'])->findOrFail($providerServiceId);
        $companies = Auth::user()->companies;
        return view('client.checkout.review', compact('ps', 'companies'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'provider_service_id' => 'required|exists:provider_services,id',
            'company_id' => 'required|exists:companies,id',
        ]);

        $user = Auth::user();
        if ($user->plan && $user->projects()->count() >= $user->plan->max_projects) {
            return redirect()->back()->withErrors(['error' => 'You have reached the maximum number of projects allowed by your plan. Please upgrade to continue.']);
        }

        $ps = ProviderService::findOrFail($request->provider_service_id);

        // 1. Create Project
        $project = Project::create([
            'client_id' => $user->id,
            'company_id' => $request->company_id,
            'provider_id' => $ps->provider_id,
            'service_id' => $ps->service_id,
            'status' => 'active',
            'total_amount' => $ps->price,
            'start_date' => now(),
        ]);

        // 2. Record Payment (Simulation)
        Payment::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'amount' => $ps->price,
            'payment_method' => 'card',
            'transaction_id' => 'TXN-' . strtoupper(Str::random(10)),
            'status' => 'held_in_escrow',
        ]);

        return redirect()->route('projects.show', $project->id)->with('success', 'Subscription active! Funds held in escrow.');
    }
}
