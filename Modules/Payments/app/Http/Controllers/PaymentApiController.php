<?php

namespace Modules\Payments\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Payments\Services\TapPaymentService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PaymentApiController extends Controller
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
        
        $transactionId = (string) Str::ulid();
        // Redirect URL for mobile might be a custom deep link or a generic success page
        $redirectUrl = url('/api/payments/mobile-callback?transaction_id=' . $transactionId);

        try {
            $checkoutUrl = $this->tapService->createCharge($amount, $customer, $redirectUrl, $isEscrow);

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

            return response()->json([
                'success' => true,
                'transaction_url' => $checkoutUrl,
                'transaction_id' => $transactionId,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment initialization failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
