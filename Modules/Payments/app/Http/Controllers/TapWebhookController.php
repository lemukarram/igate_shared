<?php

namespace Modules\Payments\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Payments\Services\TapPaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TapWebhookController extends Controller
{
    protected TapPaymentService $tapService;

    public function __construct(TapPaymentService $tapService)
    {
        $this->tapService = $tapService;
    }

    public function handle(Request $request)
    {
        // 1. Verify Signature
        $signatureHeader = $request->header('Tap-Signature');
        $payload = $request->getContent();

        if (!$this->tapService->verifyWebhookSignature($payload, $signatureHeader ?? '')) {
            Log::warning('Tap Webhook Signature Verification Failed', [
                'ip' => $request->ip(),
                'payload' => $request->all(),
            ]);
            // Return 400 or 401 to let Tap know it failed verification
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $event = $request->input('event');
        $data = $request->input('data');

        if (!$event || !$data) {
            return response()->json(['error' => 'Invalid payload structure'], 400);
        }

        // Tap usually passes our reference in 'reference.transaction'
        $transactionId = $data['reference']['transaction'] ?? null;
        $tapChargeId = $data['id'] ?? null;

        if (!$transactionId || !$tapChargeId) {
            Log::error('Tap Webhook Missing IDs', ['data' => $data]);
            return response()->json(['error' => 'Missing transaction reference'], 400);
        }

        DB::beginTransaction();

        try {
            $transaction = DB::table('transactions')->where('id', $transactionId)->lockForUpdate()->first();

            if (!$transaction) {
                DB::rollBack();
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            // Always update tap_charge_id as we might only get it here if it wasn't returned earlier
            DB::table('transactions')->where('id', $transactionId)->update([
                'tap_charge_id' => $tapChargeId,
                'updated_at' => now(),
            ]);

            // 2. Handle Events
            switch ($event) {
                case 'charge.succeeded':
                    DB::table('transactions')->where('id', $transactionId)->update(['status' => 'captured']);
                    // Here you would also dispatch events to activate subscription, release escrow, etc.
                    // event(new PaymentCaptured($transactionId));
                    break;
                
                case 'authorize.succeeded':
                    DB::table('transactions')->where('id', $transactionId)->update(['status' => 'authorized']);
                    // event(new PaymentAuthorized($transactionId));
                    break;

                case 'charge.failed':
                case 'charge.declined':
                    DB::table('transactions')->where('id', $transactionId)->update(['status' => 'failed']);
                    break;

                case 'refund.succeeded':
                    DB::table('transactions')->where('id', $transactionId)->update(['status' => 'refunded']);
                    // event(new PaymentRefunded($transactionId));
                    break;

                default:
                    Log::info("Tap Webhook Unhandled Event: {$event}", ['transaction_id' => $transactionId]);
                    break;
            }

            DB::commit();

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Tap Webhook Processing Error', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
