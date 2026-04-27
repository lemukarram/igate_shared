@extends('layouts.app')

@section('content')
<div class="max-w-7xl w-full p-4 mx-auto">
    <!-- Header -->
    <div class="mb-10 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <h1 class="text-4xl font-black text-gray-900 tracking-tight">Explore Services</h1>
        <p class="text-gray-500 text-lg mt-2 font-medium">Discover and connect with Saudi Arabia's top verified B2B service providers.</p>
    </div>

    <!-- Search Bar -->
    <div class="relative mb-10 animate-in fade-in slide-in-from-bottom-4 duration-700 delay-100">
        <i data-lucide="search" class="w-6 h-6 absolute left-5 top-1/2 -translate-y-1/2 text-gray-400"></i>
        <input type="text" placeholder="Search services, providers, or categories..." 
               class="w-full pl-14 pr-6 py-5 rounded-xl border border-gray-100 bg-white text-gray-700 focus:outline-none focus:ring-4 focus:ring-blue-50 shadow-xl shadow-blue-50/50 placeholder-gray-400 transition-all text-lg font-medium">
    </div>

    <!-- Filter Pills (Single Row) -->
    <div class="flex overflow-x-auto pb-4 mb-8 gap-3 animate-in fade-in slide-in-from-bottom-4 duration-700 delay-200 no-scrollbar">
        <button class="whitespace-nowrap px-6 py-2.5 rounded-full text-sm font-bold bg-gray-900 text-white shadow-lg shadow-gray-200 transition-all">All Services</button>
        @foreach($categories as $category)
            <button class="whitespace-nowrap px-6 py-2.5 rounded-full text-sm font-bold bg-white text-gray-500 border border-gray-100 hover:border-primary/20 hover:text-primary hover:bg-primary-light transition-all">{{ $category->category }}</button>
        @endforeach
    </div>

    <!-- Package Upgrade Options -->
    <div class="mb-16 grid grid-cols-1 md:grid-cols-3 gap-8 animate-in fade-in slide-in-from-bottom-4 duration-700 delay-300">
        <!-- Basic -->
        <div class="bg-white border border-gray-100 rounded-lg p-8 shadow-sm hover:shadow-md transition-all flex flex-col">
            <div class="mb-6">
                <h3 class="text-lg font-black text-gray-900">Basic</h3>
                <p class="text-sm text-gray-400 font-medium">For small businesses</p>
            </div>
            <div class="mb-8 flex items-baseline">
                <span class="text-4xl font-black text-gray-900">0</span>
                <span class="text-gray-400 font-bold ml-1 text-xs uppercase">SAR / month</span>
            </div>
            <ul class="space-y-4 mb-10 flex-1">
                <li class="flex items-center text-sm font-medium text-gray-600">
                    <i data-lucide="check" class="w-4 h-4 text-green-500 mr-3"></i>
                    Upto 3 services
                </li>
                <li class="flex items-center text-sm font-medium text-gray-600">
                    <i data-lucide="check" class="w-4 h-4 text-green-500 mr-3"></i>
                    Standard Support
                </li>
            </ul>
            <button class="w-full py-3 bg-gray-50 text-gray-900 rounded-lg font-bold text-sm hover:bg-gray-100 transition-all">Current Plan</button>
        </div>

        <!-- Professional -->
        <div class="bg-white border-2 border-primary rounded-lg p-8 shadow-xl shadow-primary/5 relative flex flex-col">
            <div class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1 bg-primary text-white rounded-full text-[10px] font-black uppercase tracking-widest">Most Popular</div>
            <div class="mb-6">
                <h3 class="text-lg font-black text-gray-900">Professional</h3>
                <p class="text-sm text-gray-400 font-medium">Growing companies</p>
            </div>
            <div class="mb-8 flex items-baseline">
                <span class="text-4xl font-black text-gray-900">2,500</span>
                <span class="text-gray-400 font-bold ml-1 text-xs uppercase">SAR / month</span>
            </div>
            <ul class="space-y-4 mb-10 flex-1">
                <li class="flex items-center text-sm font-medium text-gray-600">
                    <i data-lucide="check" class="w-4 h-4 text-green-500 mr-3"></i>
                    Upto 10 services
                </li>
                <li class="flex items-center text-sm font-medium text-gray-600">
                    <i data-lucide="check" class="w-4 h-4 text-green-500 mr-3"></i>
                    Priority Support
                </li>
                <li class="flex items-center text-sm font-medium text-gray-600">
                    <i data-lucide="check" class="w-4 h-4 text-green-500 mr-3"></i>
                    Account Manager
                </li>
            </ul>
            <button class="w-full py-3 bg-primary text-white rounded-lg font-bold text-sm hover:bg-primary-dark transition-all shadow-lg shadow-primary/20">Upgrade Now</button>
        </div>

        <!-- Enterprise -->
        <div class="bg-gray-900 border border-gray-800 rounded-lg p-8 shadow-sm flex flex-col text-white">
            <div class="mb-6">
                <h3 class="text-lg font-black text-white">Enterprise</h3>
                <p class="text-sm text-gray-400 font-medium">Large organizations</p>
            </div>
            <div class="mb-8 flex items-baseline">
                <span class="text-4xl font-black text-white">Custom</span>
            </div>
            <ul class="space-y-4 mb-10 flex-1">
                <li class="flex items-center text-sm font-medium text-gray-300">
                    <i data-lucide="check" class="w-4 h-4 text-primary mr-3"></i>
                    Unlimited services
                </li>
                <li class="flex items-center text-sm font-medium text-gray-300">
                    <i data-lucide="check" class="w-4 h-4 text-primary mr-3"></i>
                    24/7 Dedicated Support
                </li>
                <li class="flex items-center text-sm font-medium text-gray-300">
                    <i data-lucide="check" class="w-4 h-4 text-primary mr-3"></i>
                    Custom Integration
                </li>
            </ul>
            <button class="w-full py-3 bg-white/10 text-white rounded-lg font-bold text-sm hover:bg-white/20 transition-all">Contact Sales</button>
        </div>
    </div>

    <!-- Service Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 animate-in fade-in slide-in-from-bottom-4 duration-700 delay-400">
        @foreach($services as $service)
            @php
                $colors = [
                    'Operations' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'grad' => 'from-blue-400 to-blue-600'],
                    'Compliance' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'grad' => 'from-amber-400 to-amber-600'],
                    'Marketing' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'grad' => 'from-rose-400 to-rose-600'],
                    'Legal' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'grad' => 'from-purple-400 to-purple-600'],
                    'Finance' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'grad' => 'from-emerald-400 to-emerald-600'],
                    'Technology' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'grad' => 'from-indigo-400 to-indigo-600'],
                    'Administrative' => ['bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'grad' => 'from-slate-400 to-slate-600'],
                    'Strategy' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-600', 'grad' => 'from-orange-400 to-orange-600'],
                ];
                $c = $colors[$service->category] ?? $colors['Operations'];
            @endphp
            <div class="group bg-white rounded-lg border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-500 flex flex-col overflow-hidden relative">
                <!-- Card Header with Gradient Icon -->
                <div class="h-32 {{ $c['bg'] }} relative flex items-center justify-center transition-all group-hover:h-36 overflow-hidden">
                    <div class="w-16 h-16 rounded-xl bg-gradient-to-br {{ $c['grad'] }} flex items-center justify-center text-white shadow-xl group-hover:scale-110 transition-all duration-500 z-10">
                        <i data-lucide="{{ $service->icon }}" class="w-8 h-8"></i>
                    </div>
                </div>

                <!-- Card Content -->
                <div class="p-6 flex flex-col flex-1">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-black uppercase tracking-widest {{ $c['text'] }}">{{ $service->category }}</span>
                        <div class="flex items-center space-x-1">
                            <i data-lucide="star" class="w-3 h-3 text-amber-400 fill-current"></i>
                            <span class="text-xs font-bold text-gray-400">4.9</span>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-primary transition-colors">{{ $service->name }}</h3>
                    <p class="text-xs text-gray-500 leading-relaxed mb-6 flex-1">{{ $service->description }}</p>
                    
                    <a href="{{ route('explore.show', $service->id) }}" 
                       class="w-full py-3 rounded-lg text-sm font-bold {{ $c['bg'] }} {{ $c['text'] }} hover:bg-gray-900 hover:text-white transition-all duration-300 text-center flex items-center justify-center space-x-2">
                        <span>{{ Auth::user()->role === 'provider' ? 'Opt-in' : 'Request' }}</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection
