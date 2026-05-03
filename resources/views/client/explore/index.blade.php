@extends('layouts.app')

@section('content')
<div class="max-w-7xl w-full p-4 mx-auto" x-data="{ searchQuery: '{{ request('search') }}' }">
    <!-- Header -->
    <div class="mb-10 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <h1 class="text-4xl font-black text-gray-900 tracking-tight" x-text="lang === 'ar' ? 'استكشاف الخدمات' : 'Explore Services'"></h1>
        <p class="text-gray-500 text-lg mt-2 font-medium" x-text="lang === 'ar' ? 'اكتشف وتواصل مع أفضل مزودي خدمات الأعمال الموثقين في المملكة.' : 'Discover and connect with Saudi Arabia\'s top verified B2B service providers.'"></p>
    </div>

    <!-- Search Bar -->
    <form action="{{ route('explore.index') }}" method="GET" class="relative mb-10 animate-in fade-in slide-in-from-bottom-4 duration-700 delay-100">
        @if(request('category'))
            <input type="hidden" name="category" value="{{ request('category') }}">
        @endif
        <i data-lucide="search" class="w-6 h-6 absolute inset-y-1/2 -translate-y-1/2 start-5 text-gray-400"></i>
        <input type="text" name="search" x-model="searchQuery" :placeholder="lang === 'ar' ? 'ابحث عن الخدمات أو المزودين...' : 'Search services, providers, or categories...'" 
               class="w-full ps-14 pe-6 py-5 rounded-xl border border-gray-100 bg-white text-gray-700 focus:outline-none focus:ring-4 focus:ring-blue-50 shadow-xl shadow-blue-50/50 placeholder-gray-400 transition-all text-lg font-medium">
    </form>

    <!-- Filter Pills -->
    <div class="flex overflow-x-auto pb-4 mb-8 gap-3 animate-in fade-in slide-in-from-bottom-4 duration-700 delay-200 no-scrollbar">
        <a href="{{ route('explore.index', ['search' => request('search')]) }}" 
           class="whitespace-nowrap px-6 py-2.5 rounded-[0.5rem] text-sm font-bold {{ !request('category') ? 'bg-gray-900 text-white shadow-lg shadow-gray-200' : 'bg-white text-gray-500 border border-gray-100 hover:border-primary/20 hover:text-primary hover:bg-primary-light' }} transition-all" 
           x-text="lang === 'ar' ? 'جميع الخدمات' : 'All Services'"></a>
        @foreach($categories as $category)
            <a href="{{ route('explore.index', ['category' => $category->slug, 'search' => request('search')]) }}" 
               class="whitespace-nowrap px-6 py-2.5 rounded-[0.5rem] text-sm font-bold {{ request('category') === $category->slug ? 'bg-gray-900 text-white shadow-lg shadow-gray-200' : 'bg-white text-gray-500 border border-gray-100 hover:border-primary/20 hover:text-primary hover:bg-primary-light' }} transition-all">{{ $category->name }}</a>
        @endforeach
    </div>

    <!-- Package Upgrade Options (Hidden for now as it's repetitive, but kept for design) -->
    <!-- ... (rest of the plans) ... -->

    <!-- Service Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 animate-in fade-in slide-in-from-bottom-4 duration-700 delay-400">
        @php
            $gradients = [
                'from-blue-500 to-blue-700',
                'from-purple-500 to-purple-700',
                'from-amber-500 to-amber-700',
                'from-emerald-500 to-emerald-700',
                'from-rose-500 to-rose-700',
                'from-indigo-500 to-indigo-700',
                'from-cyan-500 to-cyan-700',
                'from-teal-500 to-teal-700'
            ];
        @endphp
        @forelse($services as $index => $service)
            <div class="group bg-white rounded-lg border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-500 flex flex-col overflow-hidden relative">
                <div class="h-32 bg-gray-50 relative flex items-center justify-center transition-all group-hover:h-36 overflow-hidden">
                    <div class="w-16 h-16 rounded-xl bg-gradient-to-br {{ $gradients[$index % count($gradients)] }} flex items-center justify-center text-white shadow-xl group-hover:scale-110 transition-all duration-500 z-10">
                        <i data-lucide="{{ $service->icon }}" class="w-8 h-8"></i>
                    </div>
                </div>

                <div class="p-6 flex flex-col flex-1">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-black uppercase tracking-widest text-primary">{{ $service->category }}</span>
                        <div class="flex items-center gap-1">
                            <i data-lucide="star" class="w-3 h-3 text-amber-400 fill-current"></i>
                            <span class="text-xs font-bold text-gray-400">4.9</span>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-primary transition-colors">{{ $service->name }}</h3>
                    <p class="text-xs text-gray-500 leading-relaxed mb-6 flex-1">{{ Str::limit($service->description, 100) }}</p>
                    
                    <a href="{{ route('explore.show', $service->id) }}" 
                       class="w-full py-3 rounded-lg text-sm font-bold bg-primary-light text-primary hover:bg-gray-900 hover:text-white transition-all duration-300 text-center flex items-center justify-center gap-2">
                        <span x-text="'{{ Auth::user()->role }}' === 'provider' ? (lang === 'ar' ? 'انضمام' : 'Opt-in') : (lang === 'ar' ? 'طلب' : 'Request')"></span>
                        <i data-lucide="arrow-right" class="w-4 h-4 flip-rtl"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center bg-gray-50 rounded-3xl border border-dashed border-gray-200">
                <i data-lucide="search-x" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900" x-text="lang === 'ar' ? 'لا توجد نتائج' : 'No services found'"></h3>
                <p class="text-gray-500" x-text="lang === 'ar' ? 'جرب البحث بكلمات أخرى.' : 'Try searching for something else.'"></p>
            </div>
        @endforelse
    </div>
</div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    [dir="rtl"] .flip-rtl { transform: scaleX(-1); }
</style>
@endsection
