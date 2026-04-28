@extends('layouts.app')

@section('content')
<div class="max-w-6xl w-full space-y-8 p-4">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div class="flex items-center space-x-4">
            <a href="{{ route('explore.index') }}" class="w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-900 transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $service->name }}</h1>
                <p class="text-gray-500">{{ $service->description }}</p>
            </div>
        </div>

        @if(auth()->user()->role === 'provider')
            <div class="flex items-center space-x-3">
                @if($providerService)
                    <div class="bg-blue-50 px-4 py-2 rounded-xl border border-blue-100 flex items-center space-x-3 mr-2">
                        <div class="text-blue-600 font-bold text-xl">{{ $clientCount }}</div>
                        <div class="text-blue-500 text-xs uppercase font-bold leading-tight">Active<br>Clients</div>
                    </div>
                    <form action="{{ route('provider.services.destroy', $providerService->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this service from your portfolio?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-6 py-3 bg-red-50 text-red-600 rounded-xl font-bold hover:bg-red-100 transition-all border border-red-100">
                            Remove Service
                        </button>
                    </form>
                @else
                    <button onclick="document.getElementById('add-service-modal').classList.remove('hidden')" class="px-6 py-3 bg-primary text-white rounded-xl font-bold hover:bg-primary-dark transition-all shadow-sm">
                        Add to My Services
                    </button>
                @endif
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-sm">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Service Scope & Subtasks</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($service->subtasks)
                        @foreach($service->subtasks as $subtask)
                            <div class="flex items-start space-x-3 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <div class="mt-1 w-5 h-5 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="check" class="w-3 h-3 text-blue-600"></i>
                                </div>
                                <span class="text-gray-700 font-medium">{{ $subtask }}</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-500 italic">No subtasks defined for this service.</p>
                    @endif
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
                            @if(auth()->user()->role === 'client')
                            <a href="{{ route('checkout.review', $ps->id) }}" class="flex-1 md:flex-none px-8 py-3 bg-primary text-white rounded-lg font-bold hover:bg-primary-dark transition-all shadow-sm text-center">
                                Request
                            </a>
                            @endif
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

        <div class="space-y-6">
            <div class="bg-blue-600 rounded-3xl p-8 text-white">
                <h3 class="text-xl font-bold mb-4">iGate Standard</h3>
                <p class="text-blue-100 mb-6 text-sm leading-relaxed">
                    All services on iGate follow strict quality guidelines and standardized delivery scopes.
                </p>
                <ul class="space-y-4">
                    <li class="flex items-center space-x-3 text-sm">
                        <i data-lucide="shield-check" class="w-5 h-5 text-blue-300"></i>
                        <span>Secure Escrow Payments</span>
                    </li>
                    <li class="flex items-center space-x-3 text-sm">
                        <i data-lucide="clock" class="w-5 h-5 text-blue-300"></i>
                        <span>SLA Guaranteed Delivery</span>
                    </li>
                    <li class="flex items-center space-x-3 text-sm">
                        <i data-lucide="check-circle" class="w-5 h-5 text-blue-300"></i>
                        <span>Verified Expert Providers</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->role === 'provider' && !$providerService)
<div id="add-service-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-[2rem] p-8 max-w-md w-full mx-4 shadow-2xl">
        <h3 class="text-2xl font-bold text-gray-900 mb-2">Add to My Services</h3>
        <p class="text-gray-500 mb-6">Define your pricing and delivery time for this standardized service.</p>
        
        <form action="{{ route('provider.services.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="service_id" value="{{ $service->id }}">
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Price (SAR)</label>
                <input type="number" name="price" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g. 1500">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Delivery Time (Days)</label>
                <input type="number" name="delivery_time_days" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g. 5">
            </div>

            <div class="flex items-center space-x-3 pt-4">
                <button type="button" onclick="document.getElementById('add-service-modal').classList.add('hidden')" class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition-all">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-6 py-3 bg-primary text-white rounded-xl font-bold hover:bg-primary-dark transition-all shadow-sm">
                    Confirm
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
