<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifying Payment...</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 h-screen flex items-center justify-center p-6">
    <div class="bg-white p-10 rounded-3xl shadow-xl shadow-blue-100 max-w-lg w-full text-center border border-gray-100">
        @if(isset($status) && $status === 'success')
            <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h2 class="text-3xl font-normal text-gray-900 mb-4">{{ $settings->success_title ?? 'Payment Successful!' }}</h2>
            <p class="text-gray-600 mb-8 leading-relaxed">
                {{ $settings->success_message ?? 'Your transaction has been confirmed. Our team is now reviewing the request. Once approved, your project workspace will become active shortly.' }}
            </p>
            
            <div class="bg-blue-50 p-6 rounded-2xl mb-8 text-left border border-blue-100">
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-500">Transaction ID</span>
                    <span class="font-medium text-gray-900">{{ $transaction_id }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Gateway Reference</span>
                    <span class="font-medium text-gray-900">{{ $tap_charge_id }}</span>
                </div>
            </div>

            <a href="{{ route('client.portfolio') }}" class="inline-flex items-center justify-center w-full py-4 bg-blue-600 text-white rounded-xl font-normal hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">
                Go to Portfolio
            </a>
        @else
            <svg class="animate-spin h-12 w-12 text-blue-600 mx-auto mb-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <h2 class="text-2xl font-normal text-gray-900 mb-2">Verifying Payment</h2>
            <p class="text-gray-500">Please wait while we verify your transaction...</p>
            <p class="text-xs text-gray-400 mt-6 italic">Transaction ID: {{ $transaction_id }}</p>
        @endif
    </div>
</body>
</html>
