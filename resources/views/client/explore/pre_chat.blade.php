@extends('layouts.app')

@section('content')
<div class="w-full h-[calc(100vh-120px)] max-w-7xl mx-auto flex flex-col space-y-6 animate-in fade-in duration-700">
    <!-- Header -->
    <div class="flex items-center justify-between bg-white p-4 rounded-lg border border-gray-100 shadow-sm">
        <div class="flex items-center space-x-4">
            <a href="{{ route('explore.show', $service->id) }}" class="w-10 h-10 bg-gray-50 border border-gray-100 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-900 transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-xl font-black text-gray-900 tracking-tight">Pre-sale Chat</h1>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $service->name }} • {{ $provider->providerProfile->company_name }}</p>
            </div>
        </div>
        <div class="flex items-center space-x-3 bg-green-50 px-4 py-2 rounded-full border border-green-100">
            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
            <span class="text-[10px] font-black uppercase text-green-600 tracking-widest">Provider Online</span>
        </div>
    </div>

    <!-- 2-Pane UI -->
    <div class="flex-1 flex gap-6 min-h-0">
        <!-- Left: Service Context (40%) -->
        <div class="w-1/3 bg-gray-50 rounded-lg p-8 border border-gray-100 overflow-y-auto custom-scrollbar flex flex-col">
            <div class="w-16 h-16 bg-white rounded-lg flex items-center justify-center text-primary shadow-lg shadow-primary/10 mb-6">
                <i data-lucide="{{ $service->icon }}" class="w-8 h-8"></i>
            </div>
            <h2 class="text-2xl font-black text-gray-900 mb-3">{{ $service->name }}</h2>
            <p class="text-gray-500 text-sm leading-relaxed font-medium mb-8">{{ $service->description }}</p>

            <div class="space-y-6 flex-1">
                <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4">Standard Package</p>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-bold text-gray-600">Price</span>
                        <span class="text-lg font-black text-primary">{{ number_format($ps->price, 0) }} SAR</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold text-gray-600">Delivery</span>
                        <span class="text-sm font-black text-gray-900">{{ $ps->delivery_time_days }} Days</span>
                    </div>
                </div>

                <div class="p-6 bg-primary-light rounded-lg border border-primary/10">
                    <p class="text-xs font-bold text-primary mb-2 flex items-center">
                        <i data-lucide="info" class="w-4 h-4 mr-2"></i>
                        Next Steps
                    </p>
                    <p class="text-[11px] text-primary/80 font-medium leading-relaxed">
                        After the consultation, you can proceed to request this service by clicking the button below. Funds will be held in escrow.
                    </p>
                </div>
            </div>

            <div class="pt-8 mt-auto">
                <a href="{{ route('checkout.review', $ps->id) }}" class="w-full py-4 bg-primary text-white rounded-lg font-black text-sm hover:bg-primary-dark transition-all shadow-lg shadow-primary/20 text-center flex items-center justify-center space-x-2">
                    <span>Request This Service</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
        </div>

        <!-- Right: Chat Window (60%) -->
        <div class="flex-1 bg-white rounded-lg border border-gray-100 shadow-sm flex flex-col overflow-hidden">
            <!-- Chat Messages -->
            <div class="flex-1 p-8 overflow-y-auto space-y-6 custom-scrollbar bg-white">
                <div class="text-center mb-8">
                    <span class="px-4 py-1 bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest rounded-full">Secure Consultation Channel</span>
                </div>

                <!-- Received Message -->
                <div class="flex items-start space-x-3 max-w-[80%]">
                    <div class="w-8 h-8 bg-primary-light rounded-lg flex items-center justify-center text-primary font-black text-xs uppercase">
                        {{ substr($provider->providerProfile->company_name, 0, 2) }}
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg rounded-tl-none border border-gray-100">
                        <p class="text-sm font-medium text-gray-700 leading-relaxed">
                            "Hello! Thank you for your interest in our {{ $service->name }} service. I'm here to answer any questions you have regarding the scope or delivery timeline."
                        </p>
                        <span class="text-[8px] font-black text-gray-300 uppercase mt-2 block">10:30 AM</span>
                    </div>
                </div>

                <!-- Sent Message Placeholder -->
                <div class="flex items-start flex-row-reverse space-x-reverse space-x-3 max-w-[80%] ml-auto">
                    <div class="w-8 h-8 bg-gray-900 rounded-lg flex items-center justify-center text-white font-black text-xs uppercase">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>
                    <div class="bg-primary p-4 rounded-lg rounded-tr-none text-white shadow-lg shadow-primary/20">
                        <p class="text-sm font-bold leading-relaxed">
                            "Hi, I wanted to confirm if this includes the final certification for ZATCA Phase 2?"
                        </p>
                        <span class="text-[8px] font-black text-white/60 uppercase mt-2 block">10:32 AM</span>
                    </div>
                </div>
            </div>

            <!-- Chat Input -->
            <div class="p-6 bg-gray-50 border-t border-gray-100">
                <div class="relative flex items-center space-x-4">
                    <div class="flex-1 relative">
                        <textarea placeholder="Type your inquiry here..." rows="1" class="w-full pl-4 pr-12 py-4 bg-white border border-gray-200 rounded-lg focus:ring-4 focus:ring-primary/10 focus:border-primary/20 outline-none transition-all font-medium text-sm resize-none"></textarea>
                        <button class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-300 hover:text-primary transition-colors">
                            <i data-lucide="paperclip" class="w-5 h-5"></i>
                        </button>
                    </div>
                    <button class="w-12 h-12 bg-primary text-white rounded-lg flex items-center justify-center hover:bg-primary-dark transition-all shadow-lg shadow-primary/20">
                        <i data-lucide="send" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
