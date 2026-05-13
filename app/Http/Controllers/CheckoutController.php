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
        $user = Auth::user();
        $companies = $user->companies;
        
        // If client has only one company, check it. If multiple, check the first one by default for early redirect.
        $targetCompany = $companies->first();

        if ($targetCompany) {
            $existingProject = Project::where('client_id', $user->id)
                ->where('company_id', $targetCompany->id)
                ->where('provider_id', $ps->provider_id)
                ->where('service_id', $ps->service_id)
                ->whereHas('transactions', function($q) {
                    $q->whereIn('status', ['authorized', 'captured']);
                })
                ->first();

            if ($existingProject) {
                return redirect()->route('projects.show', $existingProject->id)
                    ->with('info', 'You already have an active project for this service with this provider for your company ' . $targetCompany->name . '.');
            }
        }

        return view('client.checkout.review', compact('ps', 'companies'));
    }

    public function process(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'provider_service_id' => 'required|exists:provider_services,id',
            'company_id' => 'required|exists:companies,id',
        ]);

        $ps = ProviderService::findOrFail($request->provider_service_id);

        // Duplicate Check for the specific selected company
        $existingProject = Project::where('client_id', $user->id)
            ->where('company_id', $request->company_id)
            ->where('provider_id', $ps->provider_id)
            ->where('service_id', $ps->service_id)
            ->whereHas('transactions', function($q) {
                $q->whereIn('status', ['authorized', 'captured']);
            })
            ->first();

        if ($existingProject) {
            return redirect()->route('projects.show', $existingProject->id)
                ->with('info', 'An active project for this service already exists for the selected company.');
        }

        if ($user->plan && $user->projects()->where('status', 'active')->count() >= $user->plan->max_projects) {
            return redirect()->route('settings.plan.upgrade')->with('error', 'You have reached the maximum number of active projects allowed by your client plan.');
        }

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
                'number' => '500000000'
            ]
        ];

        try {
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

    public function planReview($planId)
    {
        $plan = \App\Models\Plan::findOrFail($planId);
        $user = Auth::user();
        
        if ($plan->type !== $user->role) {
            return redirect()->route('client.portfolio')->with('error', 'Invalid plan type.');
        }

        return view('client.checkout.plan_review', compact('plan'));
    }

    public function processPlan(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $plan = \App\Models\Plan::findOrFail($request->plan_id);

        if ($plan->type !== $user->role) {
            return redirect()->back()->with('error', 'Invalid plan type.');
        }

        // Initialize Tap Payment
        $transactionId = (string) Str::ulid();
        $redirectUrl = route('payments.callback', ['transaction_id' => $transactionId]);

        $customer = [
            'first_name' => $user->name,
            'email' => $user->email,
            'phone' => [
                'country_code' => '966',
                'number' => '500000000'
            ]
        ];

        try {
            // Subscription plans are ALWAYS direct charges, never escrow.
            $isEscrow = false;
            $type = 'subscription';

            $tapResponse = $this->tapService->createCharge($plan->price, $customer, $redirectUrl, $isEscrow);
            $checkoutUrl = $tapResponse['url'];
            $tapChargeId = $tapResponse['id'];

            // Record pending transaction in DB
            DB::table('transactions')->insert([
                'id' => $transactionId,
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'tap_charge_id' => $tapChargeId,
                'amount' => $plan->price,
                'currency' => 'SAR',
                'status' => 'pending',
                'type' => $type,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->away($checkoutUrl);
        } catch (\Exception $e) {
            return back()->with('error', 'Payment initialization failed. Please try again later.');
        }
    }

    public function callback(Request $request, \App\Settings\PaymentSettings $settings)
    {
        $transactionId = $request->query('transaction_id');
        $tapChargeId = $request->query('tap_id');

        if (!$tapChargeId || !$transactionId) {
            \Illuminate\Support\Facades\Log::error('Tap Callback: Missing IDs', ['tap_id' => $tapChargeId, 'transaction_id' => $transactionId]);
            return redirect()->route('client.portfolio')->with('error', 'Invalid payment response.');
        }

        try {
            // 1. Fetch real-time status from Tap API
            $charge = $this->tapService->getCharge($tapChargeId);
            
            $status = strtoupper($charge['status'] ?? 'UNKNOWN');
            
            \Illuminate\Support\Facades\Log::info('Tap Callback: Received status', [
                'transaction_id' => $transactionId,
                'tap_id' => $tapChargeId,
                'status' => $status
            ]);

            // 2. Map Tap status to our system status (Case-insensitive & Inclusive)
            $mappedStatus = 'pending';
            if (in_array($status, ['CAPTURED', 'SUCCESS', 'APPROVED'])) {
                $mappedStatus = 'captured';
            } elseif ($status === 'AUTHORIZED') {
                $mappedStatus = 'authorized';
            } elseif (in_array($status, ['DECLINED', 'FAILED', 'CANCELLED', 'ABANDONED', 'RESTRICTED', 'TIMEDOUT', 'VOID'])) {
                $mappedStatus = 'failed';
            }

            // 3. Update Transaction Record
            $transaction = DB::table('transactions')->where('id', $transactionId)->first();
            if (!$transaction) {
                \Illuminate\Support\Facades\Log::error('Tap Callback: Transaction not found in DB', ['transaction_id' => $transactionId]);
                return redirect()->route('client.portfolio')->with('error', 'Transaction not found.');
            }

            DB::table('transactions')->where('id', $transactionId)->update([
                'status' => $mappedStatus,
                'tap_charge_id' => $tapChargeId,
                'updated_at' => now(),
            ]);

            
            // 4. Handle Successful Payment Logic
            if ($mappedStatus === 'captured' || $mappedStatus === 'authorized') {
                $message = 'Payment processed successfully.';
                
                // CASE A: Subscription Plan Upgrade
                if ($transaction->plan_id && in_array($transaction->type, ['subscription', 'other']) && $mappedStatus === 'captured') {
                    $user = \App\Models\User::find($transaction->user_id);
                    
                    if ($user) {
                        \Illuminate\Support\Facades\Log::info('Tap Callback: Updating user plan', [
                            'user_id' => $user->id,
                            'old_plan_id' => $user->plan_id,
                            'new_plan_id' => $transaction->plan_id
                        ]);

                        $user->plan_id = $transaction->plan_id;
                        $user->save();
                        $user->enforcePlanLimits();
                            
                        // Record payment entry for billing history
                        DB::table('payments')->insert([
                            'user_id' => $user->id,
                            'amount' => $transaction->amount,
                            'payment_method' => 'tap',
                            'transaction_id' => $tapChargeId,
                            'status' => 'released',
                            'created_at' => now(),
                            'updated_at' => now(),
                            'project_id' => 0,
                        ]);

                        $message = 'Plan upgraded to ' . ($user->plan->name ?? 'new plan') . ' successfully!';
                        
                    }
                }

                // CASE B: Project / Service Escrow
                if ($transaction->project_id) {
                    DB::table('projects')->where('id', $transaction->project_id)->update([
                        'status' => 'active',
                        'updated_at' => now(),
                    ]);

                    // Record payment entry
                    DB::table('payments')->insert([
                        'project_id' => $transaction->project_id,
                        'user_id' => $transaction->user_id,
                        'amount' => $transaction->amount,
                        'payment_method' => 'tap',
                        'transaction_id' => $tapChargeId,
                        'status' => $mappedStatus === 'captured' ? 'released' : 'held_in_escrow',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Record history
                    \App\Models\ProjectHistory::create([
                        'project_id' => $transaction->project_id,
                        'user_id' => $transaction->user_id,
                        'action' => 'payment_confirmed',
                        'description' => 'Payment confirmed via Tap callback. Project is now active.',
                    ]);

                    \Illuminate\Support\Facades\Log::info('Tap Callback: Project Payment Success', ['project_id' => $transaction->project_id]);
                    $message = 'Payment successful! Your project is now active.';
                }

                
                return view('client.checkout.callback', [
                    'status' => $mappedStatus,
                    'message' => $message,
                    'transaction_id' => $transactionId,
                    'tap_charge_id' => $tapChargeId,
                    'project_id' => $transaction->project_id ?? null,
                ]);
            }

            // 5. Handle Failure
            if ($mappedStatus === 'failed') {
                \Illuminate\Support\Facades\Log::warning('Tap Callback: Payment Failed/Declined', ['status' => $status, 'transaction_id' => $transactionId]);
                
                $message = 'Payment ' . strtolower($status) . '. Please try again.';
                return view('client.checkout.callback', [
                    'status' => $mappedStatus,
                    'message' => $message,
                    'transaction_id' => $transactionId,
                    'tap_charge_id' => $tapChargeId,
                    'project_id' => $transaction->project_id ?? null,
                ]);
            }

            // Fallback for success/processing state
            return view('client.checkout.callback', [
                'status' => $mappedStatus,
                'message' => 'Payment status is currently: ' . $mappedStatus,
                'transaction_id' => $transactionId,
                'tap_charge_id' => $tapChargeId,
                'project_id' => $transaction->project_id ?? null,
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Tap Callback Exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('client.checkout.callback', [
                'status' => 'error',
                'message' => 'An error occurred while verifying your payment. Please contact support.',
                'transaction_id' => $transactionId,
                'tap_charge_id' => $tapChargeId,
                'project_id' => null,
            ]);
        }
    }
}
