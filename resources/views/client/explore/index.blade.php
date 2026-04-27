@extends('layouts.app')

@section('content')
<div class="max-w-6xl w-full p-4">
    <!-- Header -->
    <div class="mb-10 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <h1 class="text-4xl font-bold text-gray-900 tracking-tight">Marketplace</h1>
        <p class="text-gray-500 text-lg mt-2">Discover and connect with Saudi Arabia's top verified B2B service providers.</p>
    </div>

    <!-- Search Bar -->
    <div class="relative mb-10 animate-in fade-in slide-in-from-bottom-4 duration-700 delay-100">
        <i data-lucide="search" class="w-6 h-6 absolute left-5 top-1/2 -translate-y-1/2 text-gray-400"></i>
        <input type="text" placeholder="Search services, providers, or categories..." 
               class="w-full pl-14 pr-6 py-5 rounded-[2rem] border border-gray-100 bg-white text-gray-700 focus:outline-none focus:ring-4 focus:ring-blue-50 shadow-xl shadow-blue-50/50 placeholder-gray-400 transition-all text-lg font-medium">
    </div>

    <!-- Filter Pills -->
    <div class="flex flex-wrap gap-3 mb-12 animate-in fade-in slide-in-from-bottom-4 duration-700 delay-200">
        <button class="px-6 py-2.5 rounded-full text-sm font-bold bg-gray-900 text-white shadow-lg shadow-gray-200 transition-all">All Services</button>
        @foreach($categories as $category)
            <button class="px-6 py-2.5 rounded-full text-sm font-bold bg-white text-gray-500 border border-gray-100 hover:border-blue-200 hover:text-blue-600 hover:bg-blue-50/50 transition-all">{{ $category->category }}</button>
        @endforeach
    </div>

    <!-- Service Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 animate-in fade-in slide-in-from-bottom-4 duration-700 delay-300">
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
            <div class="group bg-white rounded-[2.5rem] border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-500 flex flex-col overflow-hidden relative">
                <!-- Card Header with Gradient Icon -->
                <div class="h-32 {{ $c['bg'] }} relative flex items-center justify-center transition-all group-hover:h-36 overflow-hidden">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br {{ $c['grad'] }} flex items-center justify-center text-white shadow-xl group-hover:scale-110 transition-all duration-500 z-10">
                        <i data-lucide="{{ $service->icon }}" class="w-8 h-8"></i>
                    </div>
                    <!-- Decorative Circles -->
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/40 rounded-full blur-2xl group-hover:scale-150 transition-all"></div>
                    <div class="absolute -left-4 -bottom-4 w-16 h-16 bg-white/20 rounded-full blur-xl group-hover:scale-125 transition-all"></div>
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
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">{{ $service->name }}</h3>
                    <p class="text-xs text-gray-500 leading-relaxed mb-6 flex-1">{{ $service->description }}</p>
                    
                    <a href="{{ route('explore.show', $service->id) }}" 
                       class="w-full py-3.5 rounded-2xl text-sm font-bold {{ $c['bg'] }} {{ $c['text'] }} hover:bg-gray-900 hover:text-white transition-all duration-300 text-center flex items-center justify-center space-x-2">
                        <span>{{ Auth::user()->role === 'provider' ? 'Opt-in to Provide' : 'View Providers' }}</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
