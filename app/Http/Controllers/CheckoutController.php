<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProviderService;
use App\Models\Project;
use App\Models\Payment;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use Modules\Payments\Services\TapPaymentService;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    protected TapPaymentService $tapService;

    public function __construct(TapPaymentService $tapService)
    {
        $this->tapService = $tapService;
    }

    public function review($providerServiceId)
    {
        $ps = ProviderService::with(['service', 'provider.providerProfile'])->findOrFail($providerServiceId);
        $companies = Auth::user()->companies;
        return view('client.checkout.review', compact('ps', 'companies'));
    }

    public function process(Request $request)
    {
        $user = Auth::user();
        if ($user->plan && $user->projects()->where('status', 'active')->count() >= $user->plan->max_projects) {
            return redirect()->route('settings.plan.upgrade')->with('error', 'You have reached the maximum number of active projects allowed by your client plan.');
        }

        $validated = $request->validate([
            'provider_service_id' => 'required|exists:provider_services,id',
            'company_id' => 'required|exists:companies,id',
        ]);

        $ps = ProviderService::findOrFail($request->provider_service_id);

        // 1. Create Project as Pending
        $project = Project::create([
            'client_id' => $user->id,
            'company_id' => $request->company_id,
            'provider_id' => $ps->provider_id,
            'service_id' => $ps->service_id,
            'provider_service_id' => $ps->id,
            'status' => 'pending_payment',
            'total_amount' => $ps->price,
            'start_date' => now(),
        ]);

        \App\Models\ProjectHistory::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'action' => 'project_initiated',
            'description' => 'Project initiated, awaiting payment confirmation.',
        ]);

        // 2. Initialize Tap Payment
        $transactionId = (string) Str::ulid();
        $redirectUrl = route('payments.callback', ['transaction_id' => $transactionId]);

        // Basic customer data extraction
        $customer = [
            'first_name' => $user->name,
            'email' => $user->email,
            'phone' => [
                'country_code' => '966',
                'number' => '500000000' // Placeholder if not in user model
            ]
        ];

        try {
            // Logic: Subscriptions are Instant Capture. Services are Escrow (Authorize).
            // This is a simple check, usually subscriptions might come from a different flow,
            // but we've implemented it here to be robust.
            $isEscrow = !str_contains(strtolower($ps->service->name), 'subscription');
            $type = $isEscrow ? 'service_escrow' : 'subscription';

            $tapResponse = $this->tapService->createCharge($ps->price, $customer, $redirectUrl, $isEscrow);
            $checkoutUrl = $tapResponse['url'];
            $tapChargeId = $tapResponse['id'];

            // Record pending transaction in DB
            DB::table('transactions')->insert([
                'id' => $transactionId,
                'user_id' => $user->id,
                'provider_id' => $ps->provider_id,
                'project_id' => $project->id,
                'tap_charge_id' => $tapChargeId,
                'amount' => $ps->price,
                'currency' => 'SAR',
                'status' => 'pending',
                'type' => $type,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->away($checkoutUrl);
        } catch (\Exception $e) {
            $project->delete(); // Clean up if payment init fails
            return back()->with('error', 'Payment initialization failed. Please try again later.');
        }
    }
}
