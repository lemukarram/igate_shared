<?php

namespace Modules\Emails\Listeners;

use Modules\Emails\Events\InvoiceGenerated;
use Modules\Emails\Services\EmailService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendInvoiceEmail implements ShouldQueue
{
    protected EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function handle(InvoiceGenerated $event): void
    {
        $invoice = $event->invoice;
        $project = $invoice->project;
        
        $client = null;
        if ($project) {
            $client = $project->client ?? ($project->company ? $project->company->users()->first() : null);
        } else {
            // Fallback for non-project invoices (e.g., subscriptions)
            $transaction = $invoice->transaction;
            $client = $transaction ? $transaction->user : null;
        }

        if ($client) {
            // Use the correct named route for downloading the invoice
            $invoiceLink = route('invoices.download', ['invoice' => $invoice->id]); 
            
            $this->emailService->sendInvoiceGenerated(
                $client->email,
                $client->name,
                $invoice->invoice_number ?? (string) $invoice->id,
                number_format($invoice->amount ?? 0, 2),
                $invoiceLink
            );
        }
    }
}
