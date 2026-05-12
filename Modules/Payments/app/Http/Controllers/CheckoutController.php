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

    public function callback(Request $request)
    {
        $transactionId = $request->query('transaction_id');
        $tapChargeId = $request->query('tap_id');

        // Here we can simply show a verifying screen. The actual status update happens via Webhook.
        // But we could also optimistically fetch the Tap charge status here if needed.
        
        return view('payments::callback', [
            'transaction_id' => $transactionId,
            'tap_charge_id' => $tapChargeId,
        ]);
    }
}
