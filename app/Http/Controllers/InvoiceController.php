<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Download the specified invoice.
     */
    public function download(Request $request, Invoice $invoice)
    {
        return $this->serveInvoice($request, $invoice);
    }

    /**
     * Download invoice starting from a transaction.
     */
    public function downloadFromTransaction(Request $request, \App\Models\Transaction $transaction)
    {
        $invoice = $transaction->invoice;

        if (!$invoice) {
            try {
                $service = app(\App\Services\InvoiceService::class);
                $invoice = $service->generateForTransaction($transaction);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Invoice Generation from Transaction failed: ' . $e->getMessage());
                return back()->with('error', 'Could not generate invoice. Please contact support.');
            }
        }

        return $this->serveInvoice($request, $invoice);
    }

    /**
     * Common logic to serve an invoice PDF.
     */
    protected function serveInvoice(Request $request, Invoice $invoice)
    {
        $user = Auth::user();
        $hasValidSignature = $request->hasValidSignature();

        // Access is allowed if:
        // 1. The request has a valid signature (from checkout callback)
        // 2. The user is logged in AND (is admin OR is client OR is provider)
        
        if (!$hasValidSignature) {
            if (!$user) {
                return redirect()->route('login')->with('info', 'Please login to download your invoice.');
            }

            $isClient = $invoice->transaction->user_id === $user->id;
            $isProvider = $invoice->transaction->provider_id === $user->id;
            $isAdmin = $user->role === 'admin';

            if (!$isClient && !$isProvider && !$isAdmin) {
                abort(403, 'You do not have permission to download this invoice.');
            }
        }

        // Always re-generate to ensure settings changes are reflected
        try {
            $service = app(\App\Services\InvoiceService::class);
            $newPath = $service->generatePdf($invoice);
            $invoice->update(['pdf_path' => $newPath]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Regenerate Invoice PDF failed: ' . $e->getMessage());
            // If we have an old one, we can still serve it as a fallback
            if (!$invoice->pdf_path || !Storage::disk('public')->exists($invoice->pdf_path)) {
                return back()->with('error', 'Could not generate invoice PDF. Please contact support.');
            }
        }

        return Storage::disk('public')->download(
            $invoice->pdf_path, 
            $invoice->invoice_number . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }
}
