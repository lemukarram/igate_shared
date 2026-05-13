<?php

namespace Modules\Payments\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Payments\Services\TapPaymentService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    protected TapPaymentService $tapService;

    public function __construct(TapPaymentService $tapService)
    {
        $this->tapService = $tapService;
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'type' => 'required|in:subscription,service_escrow',
            'project_id' => 'nullable|exists:projects,id',
            'plan_id' => 'nullable|exists:plans,id',
        ]);

        $user = $request->user();
        
        // Mock customer data for the example, typically fetched from User model
        $customer = [
            'first_name' => $user->name ?? 'John',
            'email' => $user->email ?? 'john@example.com',
            'phone' => [
                'country_code' => '966',
                'number' => '500000000'
            ]
        ];

        $amount = (float) $request->amount;
        $isEscrow = $request->type === 'service_escrow';
        
        // Generate ULID for the transaction before we send it to Tap as reference
        $transactionId = (string) Str::ulid();
        $redirectUrl = route('payments.callback', ['transaction_id' => $transactionId]);

        try {
            $checkoutUrl = $this->tapService->createCharge($amount, $customer, $redirectUrl, $isEscrow);

            // Record pending transaction in DB
            DB::table('transactions')->insert([
                'id' => $transactionId,
                'user_id' => $user->id,
                'project_id' => $request->project_id,
                'plan_id' => $request->plan_id,
                'amount' => $amount,
                'currency' => 'SAR',
                'status' => 'pending',
                'type' => $request->type,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->away($checkoutUrl);
        } catch (\Exception $e) {
            return back()->with('error', 'Payment initialization failed. Please try again.');
        }
    }

    public function callback(Request $request, \App\Settings\PaymentSettings $settings)
    {
        $transactionId = $request->query('transaction_id');
        $tapChargeId = $request->query('tap_id');

        if (!$tapChargeId) {
            return redirect()->route('client.portfolio')->with('error', 'Invalid payment response.');
        }

        try {
            $charge = $this->tapService->getCharge($tapChargeId);
            $status = $charge['status'] ?? 'UNKNOWN';

            // Check for failed statuses
            if (in_array($status, ['DECLINED', 'FAILED', 'CANCELLED', 'ABANDONED', 'RESTRICTED', 'TIMEDOUT', 'VOID'])) {
                $transaction = DB::table('transactions')->where('id', $transactionId)->first();
                if ($transaction && $transaction->project_id) {
                    $project = DB::table('projects')->where('id', $transaction->project_id)->first();
                    if ($project) {
                        return redirect()->route('checkout.review', $project->provider_service_id)
                            ->with('error', 'Payment ' . strtolower($status) . '. Please try again.');
                    }
                }
                return redirect()->route('client.portfolio')->with('error', 'Payment failed.');
            }
        } catch (\Exception $e) {
            // Log it but continue to show verifying if we can't fetch it right now
            \Illuminate\Support\Facades\Log::error('Tap Verification Error on Callback: ' . $e->getMessage());
        }

        // Fetch transaction to find related project for success/processing
        $transaction = DB::table('transactions')->where('id', $transactionId)->first();

        return view('payments::callback', [
            'transaction_id' => $transactionId,
            'tap_charge_id' => $tapChargeId,
            'status' => 'success',
            'project_id' => $transaction->project_id ?? null,
            'settings' => $settings,
        ]);
    }
}
