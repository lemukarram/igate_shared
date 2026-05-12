<?php

namespace Modules\Payments\Services;

use App\Settings\PaymentSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
     * @param array $customer e.g. ['first_name' => 'John', 'email' => 'john@test.com', 'phone' => ['country_code' => '966', 'number' => '500000000']]
     * @param string $redirectUrl
     * @param bool $isEscrow If true, it authorizes only.
     * @return string Checkout URL
     * @throws Exception
     */
    public function createCharge(float $amount, array $customer, string $redirectUrl, bool $isEscrow = false): string
    {
        try {
            $payload = [
                'amount' => $amount,
                'currency' => 'SAR',
                'customer' => $customer,
                'source' => ['id' => 'src_all'],
                'redirect' => ['url' => $redirectUrl],
            ];

            // If it's for escrow, we use the Authorize API instead of Charges API.
            $endpoint = $isEscrow ? '/authorize' : '/charges';

            $response = Http::withHeaders($this->getHeaders())
                ->post($this->baseUrl . $endpoint, $payload);

            if ($response->failed()) {
                Log::error('Tap Payment Error (Create Charge)', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);
                throw new Exception('Failed to create Tap payment charge. ' . $response->body());
            }

            $data = $response->json();

            if (!isset($data['transaction']['url'])) {
                throw new Exception('Tap payment response did not contain a transaction URL.');
            }

            return $data['transaction']['url'];
        } catch (Exception $e) {
            Log::error('Tap Payment Exception (Create Charge)', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Capture authorized funds.
     * 
     * @param string $tapChargeId The ID of the authorized charge.
     * @param float $amount The amount to capture.
     * @return array Response data
     * @throws Exception
     */
    public function captureAuthorizedFunds(string $tapChargeId, float $amount): array
    {
        try {
            $payload = [
                'amount' => $amount,
                'currency' => 'SAR',
            ];

            $response = Http::withHeaders($this->getHeaders())
                ->post($this->baseUrl . '/charges/' . $tapChargeId . '/capture', $payload);

            if ($response->failed()) {
                Log::error('Tap Payment Error (Capture)', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);
                throw new Exception('Failed to capture authorized funds. ' . $response->body());
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

            $response = Http::withHeaders($this->getHeaders())
                ->post($this->baseUrl . '/refunds', $payload);

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
