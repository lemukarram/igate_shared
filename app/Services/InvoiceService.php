<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Transaction;
use App\Settings\GeneralSettings;
use App\Settings\InvoiceSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InvoiceService
{
    public function __construct(
        protected InvoiceSettings $settings,
        protected GeneralSettings $generalSettings
    ) {}

    /**
     * Generate an invoice for a given transaction.
     */
    public function generateForTransaction(Transaction $transaction): Invoice
    {
        // Load relationships if not loaded
        $transaction->load(['user', 'project.service', 'plan', 'provider']);

        // Check if invoice already exists
        $existingInvoice = Invoice::where('transaction_id', $transaction->id)->first();
        if ($existingInvoice) {
            return $existingInvoice;
        }

        $invoiceNumber = $this->generateInvoiceNumber();
        
        $billingDetails = $this->prepareBillingDetails($transaction);

        $invoice = Invoice::create([
            'transaction_id' => $transaction->id,
            'invoice_number' => $invoiceNumber,
            'billing_details' => $billingDetails,
        ]);

        $pdfPath = $this->generatePdf($invoice);
        $invoice->update(['pdf_path' => $pdfPath]);

        return $invoice;
    }

    /**
     * Generate a unique invoice number.
     */
    protected function generateInvoiceNumber(): string
    {
        $prefix = $this->settings->invoice_prefix ?? 'IGATE-';
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(4));
        
        $number = $prefix . $date . '-' . $random;
        
        // Ensure uniqueness
        while (Invoice::where('invoice_number', $number)->exists()) {
            $random = strtoupper(Str::random(4));
            $number = $prefix . $date . '-' . $random;
        }

        return $number;
    }

    /**
     * Prepare billing details for the invoice.
     */
    protected function prepareBillingDetails(Transaction $transaction): array
    {
        $user = $transaction->user;
        $project = $transaction->project;
        $company = $project ? $project->company : ($user->companies()->first() ?? null);
        
        // Basic info
        $details = [
            'client_name' => $user->name,
            'client_email' => $user->email,
            'company_name' => $company->name ?? ($user->name),
            'company_address' => $company->address ?? '',
            'amount' => $transaction->amount,
            'currency' => $transaction->currency ?? 'SAR',
            'date' => $transaction->created_at->format('Y-m-d'),
            'type' => $transaction->type,
            'transaction_id' => $transaction->id,
        ];

        // Type specific info
        if (in_array($transaction->type, ['service', 'service_escrow']) && $project) {
            $details['item_name'] = $project->service->name ?? 'Service Fulfillment';
            $details['description'] = 'Standardized service: ' . ($project->service->name ?? '') . ' (PJ-' . $project->id . ')';
            $details['provider_name'] = $transaction->provider->name ?? ($project->provider->name ?? 'iGate Provider');
        } elseif ($transaction->type === 'subscription' && $transaction->plan) {
            $details['item_name'] = 'Subscription: ' . $transaction->plan->name;
            $details['description'] = 'iGate ' . ucfirst($transaction->plan->type) . ' Plan - ' . $transaction->plan->name;
        } else {
            $details['item_name'] = 'Business Service';
            $details['description'] = 'Standardized business service fulfillment';
        }

        return $details;
    }

    /**
     * Generate PDF for the invoice.
     */
    public function generatePdf(Invoice $invoice): string
    {
        $invoice->load('transaction.user');
        
        // Freshly resolve settings to ensure latest values from DB
        $invoiceSettings = app(InvoiceSettings::class);
        $genSettings = app(GeneralSettings::class);

        // Ensure billing details are somewhat complete for old invoices
        if (empty($invoice->billing_details['company_name'])) {
            $details = $invoice->billing_details ?? [];
            $details['company_name'] = $details['client_name'] ?? 'Client';
            $details['company_address'] = $details['company_address'] ?? '';
            $invoice->billing_details = $details;
            $invoice->save();
        }

        $data = [
            'invoice' => $invoice,
            'invoiceSettings' => $invoiceSettings,
            'generalSettings' => $genSettings,
        ];

        // Use the view we will create in Step 4
        $pdf = Pdf::loadView('invoices.pdf', $data);
        
        $filename = 'invoices/' . $invoice->invoice_number . '.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        return $filename;
    }
}
