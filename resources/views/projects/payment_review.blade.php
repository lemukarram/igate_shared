@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-[calc(100vh-100px)] bg-gray-50">
    <div class="max-w-md w-full bg-white shadow-sm border border-gray-100 p-10 text-center">
        <div class="mb-8">
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-yellow-50 text-yellow-500">
                <i data-lucide="clock" class="w-10 h-10"></i>
            </div>
        </div>
        <h2 class="text-2xl font-semibold text-gray-900 mb-4">Payment Under Review</h2>
        <p class="text-gray-500 mb-10 leading-relaxed">
            The payment for project <span class="font-medium text-gray-900">#{{ $project->id }}</span> is currently under review or pending verification. Access to the project workspace will be granted once the payment is authorized.
        </p>
        
        <div class="flex flex-col space-y-3">
            <a href="{{ route('support.contact', ['project_id' => $project->id, 'transaction_id' => $transaction?->tap_charge_id]) }}" class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white hover:bg-primary-dark transition-all font-normal">
                <i data-lucide="help-circle" class="w-4 h-4"></i>
                <span>Contact Support</span>
            </a>
            <a href="{{ url()->previous() }}" class="w-full flex items-center justify-center gap-2 px-6 py-3 border border-gray-200 text-gray-600 hover:bg-gray-50 transition-all font-normal">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Back</span>
            </a>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endsection
