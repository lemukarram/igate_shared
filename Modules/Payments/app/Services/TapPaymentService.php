<?php

namespace Modules\Payments\Services;

use App\Models\PaymentLog;
use App\Settings\PaymentSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Exception;

class TapPaymentService
{
    protected PaymentSettings $settings;
    protected string $baseUrl = 'https://api.tap.company/v2';

    public function __construct(PaymentSettings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Internal logging for Tap interactions.
     */
    protected function recordLog(string $type, string $endpoint, string $method, ?array $payload, $response): void
    {
        try {
            PaymentLog::create([
                'user_id' => Auth::id(),
                'type' => $type,
                'endpoint' => $endpoint,
                'method' => $method,
                'payload' => $payload,
                'response_body' => $response instanceof \Illuminate\Http\Client\Response ? $response->json() : $response,
                'status_code' => $response instanceof \Illuminate\Http\Client\Response ? $response->status() : null,
                'ip_address' => request()->ip(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to record Payment Log: ' . $e->getMessage());
        }
    }

    /**
     * Get the active secret key based on the current environment setting.
     */
    protected function getSecretKey(): string
    {
        return $this->settings->environment === 'sandbox' 
            ? $this->settings->sandbox_secret_key 
            : $this->settings->live_secret_key;
    }

    /**
     * Get the default headers for Tap API requests.
     */
    protected function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->getSecretKey(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Create a new charge (Authorize or Capture).
     * 
     * @param float $amount
     * @param array $customer
     * @param string $redirectUrl
     * @param bool $isEscrow
     * @return array ['url' => string, 'id' => string]
     * @throws Exception
     */
    public function createCharge(float $amount, array $customer, string $redirectUrl, bool $isEscrow = false): array
    {
        try {
            $payload = [
                'amount' => $amount,
                'currency' => 'SAR',
                'customer' => $customer,
                'source' => ['id' => 'src_all'],
                'redirect' => ['url' => $redirectUrl],
            ];

            // Handle Escrow (Authorization) with Auto-Capture from settings
            if ($isEscrow) {
                $payload['auto'] = [
                    'type' => 'CAPTURE',
                    'time' => (int) ($this->settings->auto_capture_days * 24), // Days to Hours
                ];
            }

            $endpoint = $isEscrow ? '/authorize' : '/charges';

            $response = Http::withHeaders($this->getHeaders())
                ->post($this->baseUrl . $endpoint, $payload);

            $this->recordLog('api_call', $endpoint, 'POST', $payload, $response);

            if ($response->failed()) {
                Log::error('Tap Payment Error (Create Charge)', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);
                throw new Exception('Failed to create Tap payment charge. ' . $response->body());
            }

            $data = $response->json();

            if (!isset($data['transaction']['url']) || !isset($data['id'])) {
                throw new Exception('Tap payment response missing critical data (URL or ID).');
            }

            return [
                'url' => $data['transaction']['url'],
                'id' => $data['id'],
            ];
        } catch (Exception $e) {
            Log::error('Tap Payment Exception (Create Charge)', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get charge or authorize details from Tap.
     * 
     * @param string $tapId
     * @return array
     * @throws Exception
     */
    public function getCharge(string $tapId): array
    {
        try {
            // Tap has different endpoints for Charges and Authorizations
            $endpoint = str_starts_with($tapId, 'auth_') ? '/authorize/' : '/charges/';
            
            $response = Http::withHeaders($this->getHeaders())
                ->get($this->baseUrl . $endpoint . $tapId);

            $this->recordLog('api_call', $endpoint . $tapId, 'GET', null, $response);

            if ($response->failed()) {
                Log::error('Tap Payment Error (Get Status)', [
                    'id' => $tapId,
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);
                throw new Exception('Failed to get Tap status details. ' . $response->body());
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('Tap Payment Exception (Get Status)', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Capture authorized funds.
     * 
     * @param string $authId The ID of the authorization (auth_...).
     * @param float $amount The amount to capture.
     * @return array Response data (The new Charge object)
     * @throws Exception
     */
    public function captureAuthorizedFunds(string $authId, float $amount): array
    {
        try {
            // 1. Fetch the Authorization first to get the Customer ID (Required for Charge API capture)
            $authData = $this->getCharge($authId);
            $customerId = $authData['customer']['id'] ?? null;

            if (!$customerId) {
                throw new Exception('Could not find Customer ID associated with this Authorization.');
            }

            // 2. To capture an authorization in Tap, you create a new CHARGE
            // passing the auth_id as the source.
            $payload = [
                'amount' => $amount,
                'currency' => 'SAR',
                'customer' => ['id' => $customerId],
                'source' => ['id' => $authId],
            ];

            $endpoint = '/charges';
            $response = Http::withHeaders($this->getHeaders())
                ->post($this->baseUrl . $endpoint, $payload);

            $this->recordLog('api_call', $endpoint . ' (CAPTURE)', 'POST', $payload, $response);

            if ($response->failed()) {
                Log::error('Tap Payment Error (Capture via Charge API)', [
                    'auth_id' => $authId,
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);
                throw new Exception('Failed to capture funds via Charge API. ' . $response->body());
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('Tap Payment Exception (Capture)', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Refund a charge.
     * 
     * @param string $tapChargeId The ID of the charge to refund.
     * @param float $amount The amount to refund.
     * @param string $reason The reason for the refund.
     * @return array Response data
     * @throws Exception
     */
    public function refundCharge(string $tapChargeId, float $amount, string $reason): array
    {
        try {
            $payload = [
                'charge_id' => $tapChargeId,
                'amount' => $amount,
                'currency' => 'SAR',
                'reason' => $reason,
            ];

            $endpoint = '/refunds';
            $response = Http::withHeaders($this->getHeaders())
                ->post($this->baseUrl . $endpoint, $payload);

            $this->recordLog('api_call', $endpoint, 'POST', $payload, $response);

            if ($response->failed()) {
                Log::error('Tap Payment Error (Refund)', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);
                throw new Exception('Failed to refund charge. ' . $response->body());
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('Tap Payment Exception (Refund)', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Cryptographically verify the incoming Tap webhook using the stored webhook_secret.
     * 
     * @param string $payload The raw JSON payload from the webhook request.
     * @param string $signatureHeader The 'Tap-Signature' or equivalent header value.
     * @return bool True if valid, false otherwise.
     */
    public function verifyWebhookSignature(string $payload, string $signatureHeader): bool
    {
        $secret = $this->settings->webhook_secret;

        if (empty($secret) || empty($signatureHeader)) {
            return false;
        }

        // Tap signature logic typically involves hashing the payload with the secret key using HMAC SHA256.
        // We will simulate the common signature verification logic used by Tap.
        $computedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($computedSignature, $signatureHeader);
    }
}
