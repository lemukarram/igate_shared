<?php

namespace Modules\ClientAPI\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectHistory;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Models\ProviderService;
use App\Services\InvoiceService;
use App\Traits\HandlesApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Payments\Services\TapPaymentService;

class PaymentController extends Controller
{
    use HandlesApiResponses;

    protected TapPaymentService $tapService;
    protected InvoiceService $invoiceService;

    public function __construct(TapPaymentService $tapService, InvoiceService $invoiceService)
    {
        $this->tapService = $tapService;
        $this->invoiceService = $invoiceService;
    }

    /**
     * Get details for a service request before proceeding to checkout.
     */
    public function serviceRequestDetails(Request $request)
    {
        $request->validate([
            'provider_service_id' => 'required|exists:provider_services,id',
        ]);

        try {
            $ps = ProviderService::with(['service', 'provider.providerProfile'])->findOrFail($request->provider_service_id);
            $user = auth()->user();

            $companies = $user->companies;
            
            // Identify companies that already have a successful (active) project for this specific provider and service
            $subscribedCompanyIds = Project::where('client_id', $user->id)
                ->where('provider_id', $ps->provider_id)
                ->where('service_id', $ps->service_id)
                ->where('status', 'active')
                ->pluck('company_id')
                ->toArray();

            $data = [
                'service' => [
                    'id' => $ps->service->id,
                    'name' => $ps->service->getTranslatedName(),
                    'icon' => $ps->service->icon,
                ],
                'provider' => [
                    'id' => $ps->provider->id,
                    'company_name' => $ps->provider->providerProfile->company_name ?? $ps->provider->name,
                    'logo' => $ps->provider->providerProfile->logo ? url('storage/' . $ps->provider->providerProfile->logo) : null,
                ],
                'pricing' => [
                    'monthly_price' => (float)$ps->monthly_price,
                    'annual_price' => (float)$ps->annual_price,
                    'annual_per_month' => $ps->annual_price ? (float)($ps->annual_price / 12) : null,
                    'discount_percentage' => (int)$ps->annual_discount_percentage,
                ],
                'companies' => $companies->map(function($company) use ($subscribedCompanyIds) {
                    return [
                        'id' => $company->id,
                        'name' => $company->name,
                        'is_subscribed' => in_array($company->id, $subscribedCompanyIds),
                    ];
                }),
            ];

            return $this->successResponse($data);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Initiate the checkout process and get Tap charge URL.
     */
    public function checkout(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'provider_service_id' => 'required|exists:provider_services,id',
            'company_id' => 'required|exists:companies,id',
            'billing_cycle' => 'required|in:monthly,annually',
        ]);

        try {
            $ps = ProviderService::findOrFail($request->provider_service_id);

            // Rule: Same user cannot subscribe a service of same provider account
            if ($user->id === $ps->provider_id) {
                return $this->errorResponse('You cannot subscribe to your own services.', 403);
            }

            $billingCycle = $request->billing_cycle;

            $existingProject = Project::where('client_id', $user->id)
                ->where('company_id', $request->company_id)
                ->where('provider_id', $ps->provider_id)
                ->where('service_id', $ps->service_id)
                ->where('status', 'active')
                ->first();

            if ($existingProject) {
                return $this->errorResponse('An active project for this service already exists.', 422, [
                    'project_id' => $existingProject->id
                ]);
            }

            if ($user->plan && $user->projects()->where('status', 'active')->count() >= $user->plan->max_projects) {
                return $this->errorResponse('Plan project limit reached.', 403);
            }

            $amount = ($billingCycle === 'annually') ? $ps->annual_price : $ps->monthly_price;

            return DB::transaction(function () use ($user, $ps, $request, $billingCycle, $amount) {
                $project = Project::create([
                    'client_id' => $user->id,
                    'company_id' => $request->company_id,
                    'provider_id' => $ps->provider_id,
                    'service_id' => $ps->service_id,
                    'provider_service_id' => $ps->id,
                    'status' => 'pending_payment',
                    'total_amount' => $amount,
                    'start_date' => now(),
                ]);

                ProjectHistory::create([
                    'project_id' => $project->id,
                    'user_id' => $user->id,
                    'action' => 'project_initiated',
                    'description' => 'Project initiated via API, awaiting payment.',
                ]);

                // Dispatch Service Requested Event
                event(new \Modules\Emails\Events\ServiceRequested($project));

                $transactionId = (string) \Illuminate\Support\Str::ulid();
                // For API, we might need a different callback or just a deep link. 
                // For now, using the web callback as a placeholder or a dedicated API callback if needed.
                $redirectUrl = route('payments.callback', ['transaction_id' => $transactionId]);

                $customer = [
                    'first_name' => $user->name,
                    'email' => $user->email,
                    'phone' => ['country_code' => '966', 'number' => '500000000']
                ];

                $isEscrow = !str_contains(strtolower($ps->service->name), 'subscription');
                $type = $isEscrow ? 'service_escrow' : 'subscription';

                $tapResponse = $this->tapService->createCharge($amount, $customer, $redirectUrl, $isEscrow, false);
                
                DB::table('transactions')->insert([
                    'id' => $transactionId,
                    'user_id' => $user->id,
                    'provider_id' => $ps->provider_id,
                    'project_id' => $project->id,
                    'tap_charge_id' => $tapResponse['id'],
                    'amount' => $amount,
                    'currency' => 'SAR',
                    'status' => 'pending',
                    'type' => $type,
                    'billing_cycle' => $billingCycle,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return $this->successResponse([
                    'transaction_id' => $transactionId,
                    'tap_charge_id' => $tapResponse['id'],
                    'payment_url' => $tapResponse['url'],
                    'amount' => $amount,
                    'currency' => 'SAR',
                    'project_id' => $project->id,
                ]);
            });
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Verify payment status for a transaction.
     */
    public function verifyPayment($transactionId)
    {
        try {
            $transaction = Transaction::where('id', $transactionId)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            // If already captured or authorized, return success
            if (in_array($transaction->status, ['captured', 'authorized'])) {
                $invoice = Invoice::where('transaction_id', $transaction->id)->first();
                return $this->successResponse([
                    'status' => $transaction->status,
                    'project_id' => $transaction->project_id,
                    'invoice' => $invoice ? [
                        'id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                        'amount' => $transaction->amount,
                    ] : null
                ]);
            }

            // Otherwise, check with Tap
            $charge = $this->tapService->getCharge($transaction->tap_charge_id);
            $status = strtoupper($charge['status'] ?? 'UNKNOWN');
            
            $mappedStatus = match ($status) {
                'CAPTURED', 'SUCCESS', 'APPROVED' => 'captured',
                'AUTHORIZED' => 'authorized',
                'DECLINED', 'FAILED', 'CANCELLED', 'ABANDONED' => 'failed',
                default => 'pending',
            };

            if (in_array($mappedStatus, ['captured', 'authorized'])) {
                $result = $this->finalizeTransaction($transaction, $charge, $mappedStatus);
                return $this->successResponse($result);
            }

            $transaction->update(['status' => $mappedStatus]);
            
            return $this->successResponse([
                'status' => $mappedStatus,
                'message' => 'Payment status: ' . $mappedStatus,
                'transaction_id' => $transactionId,
            ]);

        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Finalize the transaction and return details (Replicated logic from CheckoutController).
     */
    protected function finalizeTransaction($transaction, $charge, $status)
    {
        return DB::transaction(function () use ($transaction, $charge, $status) {
            $transaction = Transaction::where('id', $transaction->id)->lockForUpdate()->first();
            
            if (in_array($transaction->status, ['captured', 'authorized'])) {
                $invoice = Invoice::where('transaction_id', $transaction->id)->first();
                return [
                    'status' => $transaction->status,
                    'project_id' => $transaction->project_id,
                    'invoice' => $invoice ? [
                        'id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                    ] : null
                ];
            }

            $tapCustomerId = $charge['customer']['id'] ?? null;
            $cardToken = $charge['card']['id'] ?? null;

            $transaction->update(['status' => $status]);

            $user = User::find($transaction->user_id);
            if ($user && ($tapCustomerId || $cardToken)) {
                $user->update(['tap_customer_id' => $tapCustomerId, 'card_token' => $cardToken]);
            }

            $nextBillingDate = ($transaction->billing_cycle === 'annually') ? now()->addYear() : now()->addMonth();
            $billingPeriod = now()->format('M d, Y') . ' - ' . $nextBillingDate->format('M d, Y');

            // Handle Project Payment
            if ($transaction->project_id) {
                $project = Project::find($transaction->project_id);
                $project->update(['status' => 'active']);

                if ($transaction->type === 'subscription') {
                    Subscription::updateOrCreate(
                        [
                            'client_id' => $user->id, 
                            'service_id' => $project->service_id,
                            'company_id' => $project->company_id,
                        ],
                        [
                            'provider_id' => $project->provider_id,
                            'billing_cycle' => $transaction->billing_cycle,
                            'status' => 'active',
                            'starts_at' => now(),
                            'ends_at' => $nextBillingDate,
                            'next_billing_date' => $nextBillingDate,
                        ]
                    );
                }

                Payment::updateOrCreate(
                    ['transaction_id' => $transaction->tap_charge_id],
                    [
                        'project_id' => $project->id,
                        'user_id' => $user->id,
                        'amount' => $transaction->amount,
                        'payment_method' => 'tap',
                        'status' => $status === 'captured' ? 'released' : 'held_in_escrow',
                    ]
                );

                ProjectHistory::create([
                    'project_id' => $project->id,
                    'user_id' => $user->id,
                    'action' => 'payment_confirmed_api',
                    'description' => 'Payment confirmed via API verification.',
                ]);
            }

            $invoice = $this->invoiceService->generateForTransaction($transaction);
            $invoice->update(['billing_period' => $billingPeriod]);

            return [
                'status' => $status,
                'message' => 'Payment successful!',
                'transaction_id' => $transaction->id,
                'project_id' => $transaction->project_id,
                'invoice' => [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'pdf_url' => url('api/v1/invoices/' . $invoice->id . '/download'),
                ]
            ];
        });
    }

    /**
     * Get invoice details and download link.
     */
    public function invoiceDetails($id)
    {
        try {
            $invoice = Invoice::with('transaction')->findOrFail($id);
            
            // Check permission
            if ($invoice->transaction->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
                return $this->errorResponse('Unauthorized.', 403);
            }

            return $this->successResponse([
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'amount' => $invoice->transaction->amount,
                'currency' => $invoice->transaction->currency,
                'date' => $invoice->created_at->format('Y-m-d'),
                'billing_period' => $invoice->billing_period,
                'pdf_url' => url('api/v1/invoices/' . $invoice->id . '/download'),
                'billing_details' => $invoice->billing_details,
            ]);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Download invoice PDF.
     */
    public function downloadInvoice($id)
    {
        try {
            $invoice = Invoice::with('transaction')->findOrFail($id);
            
            // Check permission
            if ($invoice->transaction->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
                return $this->errorResponse('Unauthorized.', 403);
            }

            // Regenerate to ensure it's up to date
            $pdfPath = $this->invoiceService->generatePdf($invoice);
            $invoice->update(['pdf_path' => $pdfPath]);

            if (!Storage::disk('public')->exists($pdfPath)) {
                return $this->errorResponse('Invoice file not found.', 404);
            }

            return Storage::disk('public')->download(
                $pdfPath, 
                $invoice->invoice_number . '.pdf',
                ['Content-Type' => 'application/pdf']
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }
}
