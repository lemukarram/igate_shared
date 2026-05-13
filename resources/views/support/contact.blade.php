@extends('layouts.app')

@section('content')
<div class="h-full overflow-y-auto px-6 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-semibold text-gray-900">Contact Support</h1>
            <p class="text-gray-500 mt-2 font-light">Please fill out the form below and we will get back to you as soon as possible.</p>
        </div>

        <div class="bg-white border border-gray-100 shadow-sm overflow-hidden">
            <form action="{{ route('support.submit') }}" method="POST" class="p-8 space-y-6">
                @csrf
                <input type="hidden" name="project_id" value="{{ $project?->id }}">
                <input type="hidden" name="transaction_id" value="{{ $transactionId }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Project ID</label>
                        <input type="text" disabled value="{{ $project ? '#' . $project->id : 'N/A' }}" class="w-full bg-gray-50 border border-gray-200 py-3 px-4 text-gray-500 font-light">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Transaction ID</label>
                        <input type="text" disabled value="{{ $transactionId ?? 'N/A' }}" class="w-full bg-gray-50 border border-gray-200 py-3 px-4 text-gray-500 font-light">
                    </div>
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                    <textarea id="message" name="message" rows="8" required class="w-full border border-gray-200 py-3 px-4 focus:outline-none focus:border-primary transition-all font-light" placeholder="How can we help you?">{{ $sampleMessage }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-50">
                    <a href="{{ url()->previous() }}" class="px-6 py-3 border border-gray-200 text-gray-600 hover:bg-gray-50 transition-all font-normal">
                        Cancel
                    </a>
                    <button type="submit" class="flex items-center gap-2 px-8 py-3 bg-primary text-white hover:bg-primary-dark transition-all font-normal shadow-sm">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Send Ticket</span>
                    </button>
                </div>
            </form>
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
