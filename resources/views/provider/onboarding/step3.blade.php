@extends('layouts.app')

@section('content')
<div class="max-w-2xl w-full bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-gray-900">Step 3: Banking Info</h2>
            <span class="text-sm font-medium text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Step 3 of 3</span>
        </div>
        <p class="text-gray-500">Provide your banking details to receive payouts.</p>
    </div>

    <form action="{{ route('provider.onboarding.step3.post') }}" method="POST" class="space-y-6">
        @csrf
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Bank Name</label>
            <input type="text" name="bank_name" value="{{ old('bank_name', $profile->bank_name) }}" required
                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">IBAN</label>
            <input type="text" name="iban" value="{{ old('iban', $profile->iban) }}" required
                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all"
                   placeholder="SA00 0000 0000 0000 0000 0000">
        </div>

        <div class="pt-4 flex space-x-4">
            <a href="{{ route('provider.onboarding.step2') }}" class="flex-1 py-4 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition-all text-center">
                Back
            </a>
            <button type="submit" class="flex-[2] py-4 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-100 flex items-center justify-center space-x-2">
                <span>Complete Onboarding</span>
                <i data-lucide="check-circle" class="w-5 h-5"></i>
            </button>
        </div>
    </form>
</div>
@endsection
