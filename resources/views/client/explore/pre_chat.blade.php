@extends('layouts.app')

@section('content')
<div class="w-full h-full max-w-7xl mx-auto flex flex-col space-y-6 animate-in fade-in duration-700">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('explore.index') }}" class="w-10 h-10 bg-gray-50 border border-gray-100 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-900 transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Pre-sale Consultation</h1>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-widest">Inquiry for {{ $service->name }}</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
            <span class="text-xs font-black uppercase text-gray-400 tracking-widest">Provider Online</span>
        </div>
    </div>

    <!-- 2-Pane UI -->
    <div class="flex-1 flex gap-6 overflow-hidden">
        <!-- Left: Service Context (50%) -->
        <div class="w-1/2 bg-gray-50 rounded-[3rem] p-10 border border-gray-100 overflow-y-auto custom-scrollbar flex flex-col">
            <div class="w-20 h-20 bg-white rounded-3xl flex items-center justify-center text-blue-600 shadow-xl shadow-blue-100 mb-8">
                <i data-lucide="{{ $service->icon }}" class="w-10 h-10"></i>
            </div>
            <h2 class="text-3xl font-black text-gray-900 mb-4">{{ $service->name }}</h2>
            <p class="text-gray-600 leading-relaxed font-medium mb-10">{{ $service->description }}</p>

            <div class="space-y-8 flex-1">
                <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                    <h3 class="font-black text-xs uppercase tracking-[0.2em] text-gray-400 mb-6 italic">Verified Provider</h3>
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 font-black text-xl">
                            {{ substr($provider->providerProfile->company_name, 0, 2) }}
                        </div>
                        <div>
                            <p class="font-black text-gray-900 text-lg">{{ $provider->providerProfile->company_name }}</p>
                            <div class="flex items-center text-xs font-bold text-gray-400">
                                <i data-lucide="star" class="w-3 h-3 text-amber-400 fill-current mr-1"></i>
                                5.0 (48 reviews)
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white p-6 rounded-[2rem] border border-gray-100">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Standard Price</p>
                        <p class="text-2xl font-black text-blue-600">{{ number_format($ps->price, 0) }} <span class="text-xs">SAR</span></p>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] border border-gray-100">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Standard SLA</p>
                        <p class="text-2xl font-black text-gray-900">{{ $ps->delivery_time_days }} <span class="text-xs uppercase">Days</span></p>
                    </div>
                </div>
            </div>

            <div class="pt-10">
                <a href="{{ route('checkout.review', $ps->id) }}" class="w-full py-5 bg-gray-900 text-white rounded-[2rem] font-black text-lg hover:bg-black transition-all shadow-xl shadow-gray-200 text-center flex items-center justify-center space-x-3">
                    <span>Proceed to Subscription</span>
                    <i data-lucide="arrow-right" class="w-5 h-5"></i>
                </a>
            </div>
        </div>

        <!-- Right: Chat Window (50%) -->
        <div class="w-1/2 bg-white rounded-[3rem] border border-gray-100 shadow-xl shadow-gray-100/50 flex flex-col overflow-hidden">
            <!-- Chat Messages -->
            <div class="flex-1 p-8 overflow-y-auto space-y-6 custom-scrollbar bg-white">
                <!-- Received Message -->
                <div class="flex items-start space-x-3 max-w-[85%]">
                    <div class="w-8 h-8 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 font-bold text-xs">iG</div>
                    <div class="bg-gray-50 p-5 rounded-2xl rounded-tl-none border border-gray-100">
                        <p class="text-sm font-medium text-gray-700 leading-relaxed italic">
                            "Welcome to iGate Shared Services. I'm the lead manager for this service. How can I help clarify the scope for your business?"
                        </p>
                    </div>
                </div>

                <!-- Sent Message Placeholder -->
                <div class="flex items-start flex-row-reverse space-x-reverse space-x-3 max-w-[85%] ml-auto">
                    <div class="w-8 h-8 bg-gray-900 rounded-xl flex items-center justify-center text-white font-bold text-xs">ME</div>
                    <div class="bg-blue-600 p-5 rounded-2xl rounded-tr-none text-white shadow-lg shadow-blue-100">
                        <p class="text-sm font-bold leading-relaxed">
                            Looking to verify if the ZATCA compliance covers multiple branches.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Chat Input -->
            <div class="p-6 bg-gray-50 border-t border-gray-100">
                <div class="relative">
                    <textarea placeholder="Type your inquiry..." rows="1" class="w-full pl-6 pr-14 py-5 bg-white border border-gray-200 rounded-[2rem] focus:ring-4 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all font-medium text-sm resize-none"></textarea>
                    <button class="absolute right-3 bottom-3 w-10 h-10 bg-blue-600 text-white rounded-2xl flex items-center justify-center hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">
                        <i data-lucide="send" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
