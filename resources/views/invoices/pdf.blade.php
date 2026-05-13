<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; line-height: 1.5; margin: 0; padding: 0; }
        .container { padding: 40px; }
        .header { margin-bottom: 40px; width: 100%; clear: both; }
        .header-left { float: left; width: 50%; }
        .header-right { float: right; width: 50%; text-align: right; }
        .logo { max-height: 60px; margin-bottom: 10px; }
        .invoice-title { font-size: 24px; font-weight: normal; color: #000; margin: 0; }
        .details { margin-bottom: 40px; width: 100%; clear: both; padding-top: 20px; }
        .details-col { float: left; width: 50%; }
        .label { color: #999; text-transform: uppercase; font-size: 10px; letter-spacing: 1px; margin-bottom: 5px; }
        .value { font-weight: bold; margin-bottom: 15px; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 40px; clear: both; }
        table.items th { background: #f9f9f9; text-align: left; padding: 12px; border-bottom: 2px solid #eee; color: #999; font-size: 10px; text-transform: uppercase; }
        table.items td { padding: 12px; border-bottom: 1px solid #eee; }
        .total-section { float: right; width: 250px; margin-top: 20px; }
        .total-row { width: 100%; margin-bottom: 10px; clear: both; }
        .total-label { float: left; color: #999; width: 50%; }
        .total-value { float: right; text-align: right; font-weight: bold; font-size: 16px; width: 50%; }
        .footer { margin-top: 60px; border-top: 1px solid #eee; padding-top: 20px; color: #999; font-size: 10px; clear: both; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header clearfix">
            <div class="header-left">
                @if($settings->logo)
                    <img src="{{ public_path('storage/' . $settings->logo) }}" class="logo">
                @else
                    <h1 class="invoice-title">iGate</h1>
                @endif
                <p>{{ $settings->address }}<br>{{ $settings->contact_info }}<br>TRN: {{ $settings->tax_id }}</p>
            </div>
            <div class="header-right">
                <h1 class="invoice-title">INVOICE</h1>
                <p>#{{ $invoice->invoice_number }}<br>Date: {{ $invoice->created_at->format('M d, Y') }}</p>
            </div>
        </div>

        <div class="details clearfix">
            <div class="details-col">
                <div class="label">Bill To</div>
                <div class="value">
                    {{ $invoice->billing_details['client_name'] }}<br>
                    {{ $invoice->billing_details['client_email'] }}
                </div>
            </div>
            <div class="details-col" style="text-align: right;">
                <div class="label">Payment Status</div>
                <div class="value" style="color: #10b981;">PAID</div>
            </div>
        </div>

        <table class="items">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $invoice->billing_details['item_name'] }}</td>
                    <td style="text-align: right;">{{ number_format($invoice->billing_details['amount'], 2) }} {{ $invoice->billing_details['currency'] }}</td>
                </tr>
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row clearfix">
                <div class="total-label">Subtotal</div>
                <div class="total-value">{{ number_format($invoice->billing_details['amount'], 2) }} {{ $invoice->billing_details['currency'] }}</div>
            </div>
            <div class="total-row clearfix" style="margin-top: 20px; border-top: 2px solid #000; padding-top: 10px;">
                <div class="total-label" style="color: #000; font-weight: bold;">Total</div>
                <div class="total-value" style="color: #000; font-size: 20px;">{{ number_format($invoice->billing_details['amount'], 2) }} {{ $invoice->billing_details['currency'] }}</div>
            </div>
        </div>

        <div class="footer clearfix">
            <p><strong>Notes:</strong> {{ $settings->thank_you_note }}</p>
            <p><strong>Terms:</strong> {{ $settings->terms_conditions }}</p>
        </div>
    </div>
</body>
</html>
