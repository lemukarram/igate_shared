@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-10 rounded-3xl shadow-sm border border-gray-100 text-center mt-10">
    @if(in_array($status, ['captured', 'authorized', 'success']))
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i data-lucide="check-circle" class="w-10 h-10 text-green-600"></i>
        </div>
        <h2 class="text-3xl font-normal text-gray-900 mb-2" x-text="t('common.payment_successful')"></h2>
        <p class="text-gray-500 mb-8" x-text="t('common.transaction_processed_success')"></p>
    @else
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i data-lucide="x-circle" class="w-10 h-10 text-red-600"></i>
        </div>
        <h2 class="text-3xl font-normal text-gray-900 mb-2" x-text="t('common.payment_failed')"></h2>
        <p class="text-gray-500 mb-8" x-text="t('common.payment_processing_issue')"></p>
    @endif

    <div class="bg-gray-50 rounded-2xl p-6 text-start mb-8 space-y-3">
        <div class="flex items-center justify-between text-sm">
            <span class="text-gray-500" x-text="t('common.transaction_id')"></span>
            <span class="font-normal text-gray-900">{{ $transaction_id }}</span>
        </div>
        <div class="flex items-center justify-between text-sm">
            <span class="text-gray-500" x-text="t('common.tap_reference')"></span>
            <span class="font-normal text-gray-900">{{ $tap_charge_id }}</span>
        </div>
        <div class="flex items-center justify-between text-sm border-t border-gray-200 pt-3 mt-3">
            <span class="text-gray-500" x-text="t('common.status')"></span>
            <span class="font-normal uppercase px-2 py-0.5 rounded text-[10px] {{ in_array($status, ['captured', 'authorized', 'success']) ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                {{ $status }}
            </span>
        </div>
    </div>

    <div class="flex justify-center space-x-4">
        @php
            $transaction = \App\Models\Transaction::with('invoice')->find($transaction_id);
            $invoice = $transaction ? $transaction->invoice : null;
        @endphp
        
        @if($invoice)
            <a href="{{ Storage::disk('public')->url($invoice->pdf_path) }}" target="_blank" class="px-6 py-3 border border-gray-200 text-gray-700 rounded-xl font-normal hover:bg-gray-50 transition-all flex items-center gap-2">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>{{ __('common.download_invoice') ?? 'Download Invoice' }}</span>
            </a>
        @endif

        @if(isset($project_id) && $project_id)
            <a href="{{ route('projects.show', $project_id) }}" class="px-6 py-3 bg-primary text-white rounded-xl font-normal hover:bg-primary-dark transition-all">
                <span x-text="t('common.view_project')"></span>
            </a>
        @else
            <a href="{{ route('client.portfolio') }}" class="px-6 py-3 bg-primary text-white rounded-xl font-normal hover:bg-primary-dark transition-all">
                <span x-text="t('common.return_dashboard')"></span>
            </a>
        @endif
    </div>
</div>
@endsection
