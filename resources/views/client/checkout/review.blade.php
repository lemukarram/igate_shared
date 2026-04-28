@extends('layouts.app')

@section('content')
<div class="max-w-4xl w-full bg-white p-10 rounded-3xl shadow-sm border border-gray-100 flex flex-col md:flex-row gap-12">
    <!-- Left: Order Summary -->
    <div class="flex-1 space-y-8">
        <div>
            <h2 class="text-3xl font-black text-gray-900 mb-2">Review & Subscribe</h2>
            <p class="text-gray-500">Confirm your subscription to this standardized service.</p>
        </div>

        <div class="bg-blue-50/50 p-6 rounded-3xl border border-blue-100 space-y-4">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white">
                    <i data-lucide="{{ $ps->service->icon }}" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">{{ $ps->service->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $ps->provider->providerProfile->company_name }}</p>
                </div>
            </div>
            
            <div class="pt-4 border-t border-blue-100 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Service Fee</span>
                    <span class="font-bold text-gray-900">{{ number_format($ps->price, 2) }} SAR</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Platform Fee (0%)</span>
                    <span class="font-bold text-gray-900">0.00 SAR</span>
                </div>
                <div class="flex justify-between text-lg pt-2 border-t border-blue-100">
                    <span class="font-black text-gray-900">Total</span>
                    <span class="font-black text-blue-600">{{ number_format($ps->price, 2) }} SAR</span>
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-3 text-sm text-gray-500 bg-gray-50 p-4 rounded-2xl">
            <i data-lucide="shield-check" class="w-5 h-5 text-green-500"></i>
            <p>Your funds will be held in **iGate Shared Services Escrow** and only released to the provider upon your approval of milestones.</p>
        </div>
    </div>

    <!-- Right: Payment Simulation -->
    <div class="flex-1 space-y-6">
        <h3 class="text-xl font-bold text-gray-900">Payment Method</h3>
        
        <form action="{{ route('checkout.process') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="provider_service_id" value="{{ $ps->id }}">
            
            @if($errors->has('error'))
                <div class="p-4 bg-red-50 text-red-600 rounded-2xl text-sm font-bold border border-red-100">
                    {{ $errors->first('error') }}
                </div>
            @endif

            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Select Company</label>
                <select name="company_id" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                    @if($companies->count() == 0)
                        <option value="" disabled selected>No companies found. Please add a company in your portfolio first.</option>
                    @else
                        <option value="" disabled selected>Choose a company to assign this project...</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    @endif
                </select>
                @error('company_id')
                    <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-4 pt-4">
                <div class="p-4 border-2 border-blue-600 bg-blue-50/30 rounded-2xl flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="credit-card" class="w-6 h-6 text-blue-600"></i>
                        <span class="font-bold">Credit / Mada Card</span>
                    </div>
                    <div class="flex space-x-1">
                        <div class="w-8 h-5 bg-gray-200 rounded"></div>
                        <div class="w-8 h-5 bg-gray-200 rounded"></div>
                    </div>
                </div>

                <div class="space-y-3">
                    <input type="text" placeholder="Card Number" value="4242 4242 4242 4242" disabled
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-400">
                    <div class="flex gap-4">
                        <input type="text" placeholder="MM/YY" value="12/26" disabled
                               class="flex-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-400">
                        <input type="text" placeholder="CVC" value="123" disabled
                               class="flex-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-400">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full py-4 bg-blue-600 text-white rounded-xl font-black hover:bg-blue-700 transition-all shadow-xl shadow-blue-100 flex items-center justify-center space-x-2">
                <span>Confirm & Pay {{ number_format($ps->price, 0) }} SAR</span>
            </button>
            <p class="text-center text-xs text-gray-400">Secure transaction powered by iGate Shared Services Escrow.</p>
        </form>
    </div>
</div>
@endsection
