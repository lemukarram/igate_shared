@extends('layouts.app')

@section('content')
<div class="max-w-6xl w-full space-y-8 p-4">
    <div class="flex items-center space-x-4 mb-8">
        <a href="{{ route('explore.index') }}" class="w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-900 transition-colors">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $service->name }}</h1>
            <p class="text-gray-500">{{ $service->description }}</p>
        </div>
    </div>

    <div class="space-y-6">
        <h2 class="text-xl font-bold text-gray-900">Available Providers</h2>
        
        <div class="grid grid-cols-1 gap-4">
            @forelse($providers as $ps)
            <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0">
                <div class="flex items-center space-x-4 flex-1">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center overflow-hidden">
                        @if($ps->provider->providerProfile->logo)
                            <img src="{{ asset('storage/' . $ps->provider->providerProfile->logo) }}" class="w-full h-full object-cover">
                        @else
                            <i data-lucide="building-2" class="w-8 h-8 text-gray-400"></i>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $ps->provider->providerProfile->company_name }}</h3>
                        <div class="flex items-center space-x-2 text-sm text-gray-500">
                            <span class="flex items-center"><i data-lucide="star" class="w-4 h-4 text-yellow-400 fill-current mr-1"></i> 5.0</span>
                            <span>•</span>
                            <span>Verified Provider</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-12 px-8 border-x border-gray-50 hidden lg:flex">
                    <div class="text-center">
                        <span class="block text-xs text-gray-400 uppercase font-semibold">Delivery</span>
                        <span class="text-sm font-bold text-gray-900">{{ $ps->delivery_time_days }} Days</span>
                    </div>
                    <div class="text-center">
                        <span class="block text-xs text-gray-400 uppercase font-semibold">Price</span>
                        <span class="text-xl font-black text-blue-600">{{ number_format($ps->price, 0) }} SAR</span>
                    </div>
                </div>

                <div class="flex items-center space-x-3 w-full md:w-auto">
                    <a href="{{ route('explore.chat', ['serviceId' => $service->id, 'providerId' => $ps->provider_id]) }}" 
                       class="flex-1 md:flex-none px-6 py-3 bg-gray-50 text-gray-700 rounded-xl font-bold hover:bg-gray-100 transition-all border border-gray-100 text-center">
                        Chat
                    </a>
                    <a href="{{ route('checkout.review', $ps->id) }}" class="flex-1 md:flex-none px-8 py-3 bg-primary text-white rounded-lg font-bold hover:bg-primary-dark transition-all shadow-sm text-center">
                        Request
                    </a>
                </div>
            </div>
            @empty
            <div class="py-20 text-center bg-gray-50 rounded-3xl border border-dashed border-gray-200">
                <i data-lucide="users-2" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900">No providers yet</h3>
                <p class="text-gray-500">We are currently onboarding experts for this service.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
