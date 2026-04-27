<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProviderService;
use App\Models\Project;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function review($providerServiceId)
    {
        $ps = ProviderService::with(['service', 'provider.providerProfile'])->findOrFail($providerServiceId);
        return view('client.checkout.review', compact('ps'));
    }

    public function process(Request $request)
    {
        $ps = ProviderService::findOrFail($request->provider_service_id);

        // 1. Create Project
        $project = Project::create([
            'client_id' => Auth::id(),
            'provider_id' => $ps->provider_id,
            'service_id' => $ps->service_id,
            'status' => 'active',
            'total_amount' => $ps->price,
            'start_date' => now(),
        ]);

        // 2. Record Payment (Simulation)
        Payment::create([
            'project_id' => $project->id,
            'user_id' => Auth::id(),
            'amount' => $ps->price,
            'payment_method' => 'card',
            'transaction_id' => 'TXN-' . strtoupper(Str::random(10)),
            'status' => 'held_in_escrow',
        ]);

        return redirect()->route('projects.show', $project->id)->with('success', 'Subscription active! Funds held in escrow.');
    }
}
