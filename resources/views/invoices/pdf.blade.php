<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #333; line-height: 1.6; margin: 0; padding: 0; }
        .container { padding: 30px; max-width: 800px; margin: auto; }
        .header { margin-bottom: 30px; width: 100%; border-bottom: 2px solid #3da9e4; padding-bottom: 20px; }
        .header table { width: 100%; border: none; }
        .logo { max-height: 50px; margin-bottom: 10px; }
        .invoice-title { font-size: 20px; color: #3da9e4; margin: 0; text-transform: uppercase; letter-spacing: 1px; }
        .company-details { font-size: 10px; color: #666; }
        
        .info-boxes { width: 100%; margin-bottom: 30px; margin-top: 20px; }
        .info-box { width: 50%; vertical-align: top; }
        .label { color: #999; text-transform: uppercase; font-size: 9px; letter-spacing: 1px; margin-bottom: 4px; font-weight: bold; }
        .value { font-size: 11px; margin-bottom: 12px; }
        
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table.items th { background: #f8fafc; text-align: left; padding: 10px; border-bottom: 1px solid #e2e8f0; color: #64748b; font-size: 9px; text-transform: uppercase; }
        table.items td { padding: 12px 10px; border-bottom: 1px solid #f1f5f9; }
        
        .totals-container { width: 100%; }
        .totals-box { float: right; width: 200px; }
        .total-row { width: 100%; margin-bottom: 5px; clear: both; padding: 5px 0; }
        .total-label { float: left; color: #64748b; width: 50%; }
        .total-value { float: right; text-align: right; font-weight: bold; width: 50%; }
        .grand-total { border-top: 2px solid #3da9e4; margin-top: 20px; padding-top: 2px; color: #3da9e4; }
        .grand-total .total-value { font-size: 16px; }
        
        .footer { margin-top: 50px; border-top: 1px solid #eee; padding-top: 20px; color: #94a3b8; font-size: 9px; text-align: center; }
        .status-badge { display: inline-block; padding: 4px 10px; border-radius: 4px; background: #dcfce7; color: #166534; font-weight: bold; font-size: 10px; text-transform: uppercase; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <table>
                <tr>
                    <td style="width: 50%;">
                        @php
                            $logoPath = null;
                            if (!empty($invoiceSettings->logo) && file_exists(storage_path('app/public/' . $invoiceSettings->logo))) {
                                $logoPath = storage_path('app/public/' . $invoiceSettings->logo);
                            } elseif (!empty($generalSettings->logo)) {
                                if (file_exists(public_path($generalSettings->logo))) {
                                    $logoPath = public_path($generalSettings->logo);
                                } elseif (file_exists(storage_path('app/public/' . $generalSettings->logo))) {
                                    $logoPath = storage_path('app/public/' . $generalSettings->logo);
                                }
                            }
                        @endphp

                        @if($logoPath)
                            <img src="{{ $logoPath }}" class="logo">
                        @else
                            <h1 style="color: #3da9e4; margin: 0;">{{ $invoiceSettings->company_name ?? ($generalSettings->site_name ?? 'iGate') }}</h1>
                        @endif
                        <div class="company-details">
                            {{ $invoiceSettings->company_name ?? ($generalSettings->site_name ?? 'iGate Shared Services') }}<br>
                            {{ $invoiceSettings->address ?? '' }}<br>
                            {{ $invoiceSettings->contact_info ?? '' }}<br>
                            <strong>TRN: {{ $invoiceSettings->tax_id ?? '' }}</strong>
                        </div>
                    </td>
                    <td style="width: 50%; text-align: right; vertical-align: top;">
                        <h1 class="invoice-title">Tax Invoice</h1>
                        <p style="margin-top: 5px; color: #64748b;">
                            Invoice #: <strong>{{ $invoice->invoice_number }}</strong><br>
                            Date: <strong>{{ $invoice->created_at->format('M d, Y') }}</strong><br>
                            Transaction ID: <strong>{{ substr($invoice->transaction_id ?? '', -12) }}</strong>
                        </p>
                        <div class="status-badge">Paid</div>
                    </td>
                </tr>
            </table>
        </div>

        <table class="info-boxes">
            <tr>
                <td class="info-box">
                    <div class="label">Bill To</div>
                    <div class="value">
                        <strong>{{ $invoice->billing_details['company_name'] ?? ($invoice->billing_details['client_name'] ?? 'Client') }}</strong><br>
                        Attn: {{ $invoice->billing_details['client_name'] ?? 'N/A' }}<br>
                        {{ $invoice->billing_details['client_email'] ?? '' }}<br>
                        {{ $invoice->billing_details['company_address'] ?? '' }}
                    </div>
                </td>
                <td class="info-box" style="text-align: right;">
                    <div class="label">Service Provider</div>
                    <div class="value">
                        <strong>{{ $invoice->billing_details['provider_name'] ?? 'iGate Marketplace' }}</strong><br>
                        Verified Partner<br>
                        KSA Business Network
                    </div>
                </td>
            </tr>
        </table>

        <table class="items">
            <thead>
                <tr>
                    <th style="width: 70%;">Description</th>
                    <th style="width: 30%; text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div style="font-weight: bold; margin-bottom: 4px;">{{ $invoice->billing_details['item_name'] ?? 'Business Service' }}</div>
                        <div style="color: #64748b; font-size: 10px;">{{ $invoice->billing_details['description'] ?? '' }}</div>
                    </td>
                    <td style="text-align: right; vertical-align: top; font-weight: bold;">
                        {{ number_format($invoice->billing_details['amount'] ?? 0, 2) }} {{ $invoice->billing_details['currency'] ?? 'SAR' }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="clearfix">
            <div class="totals-box">
                <div class="total-row clearfix">
                    <div class="total-label">Subtotal</div>
                    <div class="total-value">{{ number_format($invoice->billing_details['amount'] ?? 0, 2) }} {{ $invoice->billing_details['currency'] ?? 'SAR' }}</div>
                </div>
                <div class="total-row clearfix">
                    <div class="total-label">Tax (0%)</div>
                    <div class="total-value">0.00 {{ $invoice->billing_details['currency'] ?? 'SAR' }}</div>
                </div>
                <div class="total-row clearfix grand-total">
                    <div class="total-label" style="font-weight: bold;">Total Amount</div>
                    <div class="total-value">{{ number_format($invoice->billing_details['amount'] ?? 0, 2) }} {{ $invoice->billing_details['currency'] ?? 'SAR' }}</div>
                </div>
            </div>
        </div>

        <div style="margin-top: 40px; font-size: 10px; color: #64748b;">
            @if(!empty($invoiceSettings->thank_you_note))
                <div class="label">Notes</div>
                <p>{{ $invoiceSettings->thank_you_note }}</p>
            @endif
            @if(!empty($invoiceSettings->terms_conditions))
                <div class="label" style="margin-top: 15px;">Terms & Conditions</div>
                <p>{{ $invoiceSettings->terms_conditions }}</p>
            @endif
        </div>

        <div class="footer">
            <p>This is a computer-generated document. No signature is required.</p>
            <p>&copy; {{ date('Y') }} {{ $invoiceSettings->company_name ?? ($generalSettings->site_name ?? 'iGate Shared Services') }} • Riyadh, Kingdom of Saudi Arabia</p>
        </div>
    </div>
</body>
</html>
