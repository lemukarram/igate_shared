@extends('layouts.app')

@section('content')
<div class="max-w-7xl w-full p-4 mx-auto" x-data="{ searchQuery: '{{ request('search') }}' }">
    <!-- Header -->
    <div class="flex pb-10 flex-col md:flex-row md:items-center justify-between gap:4">
        <div>
            <h1 class="text-2xl font-normal text-gray-900" x-text="t('common.explore_services')"></h1>
            <p class="text-gray-500 mt-1" x-text="t('common.discover_connect_subtitle')"></p>
        </div>
        @if(Auth::user()->role === 'provider')
            <button @click="addServiceOpen = true" class="px-6 py-3 bg-primary text-white rounded-xl font-normal hover:bg-primary-dark transition-all flex items-center gap-2 shadow-lg shadow-primary/20">
                <i data-lucide="plus" class="w-5 h-5"></i>
                <span x-text="t('explore.add_new_service')"></span>
            </button>
        @endif
    </div>



    <!-- Search Bar -->
    <form action="{{ route('explore.index') }}" method="GET" class="relative mb-10 animate-in fade-in slide-in-from-bottom-4 duration-700 delay-100">
        @if(request('category'))
            <input type="hidden" name="category" value="{{ request('category') }}">
        @endif
        <i data-lucide="search" class="w-6 h-6 absolute inset-y-1/2 -translate-y-1/2 start-5 text-gray-400"></i>
        <input type="text" name="search" x-model="searchQuery" :placeholder="t('common.search_placeholder')" 
               class="w-full ps-14 pe-6 py-5 rounded-xl border border-gray-100 bg-white text-gray-700 focus:outline-none focus:ring-4 focus:ring-blue-50 shadow-xl shadow-blue-50/50 placeholder-gray-400 transition-all text-lg font-normal">
    </form>

    <!-- Filter Pills -->
    <div class="flex overflow-x-auto pb-4 mb-8 gap-3 animate-in fade-in slide-in-from-bottom-4 duration-700 delay-200 no-scrollbar">
        <a href="{{ route('explore.index', ['search' => request('search')]) }}" 
           class="whitespace-nowrap px-6 py-2.5 rounded-[0.5rem] text-sm font-normal {{ !request('category') ? 'bg-gray-900 text-white shadow-lg shadow-gray-200' : 'bg-white text-gray-500 border border-gray-100 hover:border-primary/20 hover:text-primary hover:bg-primary-light' }} transition-all" 
           x-text="t('common.all_services')"></a>
        @foreach($categories as $category)
            <a href="{{ route('explore.index', ['category' => $category->slug, 'search' => request('search')]) }}" 
               class="whitespace-nowrap px-6 py-2.5 rounded-[0.5rem] text-sm font-normal {{ request('category') === $category->slug ? 'bg-gray-900 text-white shadow-lg shadow-gray-200' : 'bg-white text-gray-500 border border-gray-100 hover:border-primary/20 hover:text-primary hover:bg-primary-light' }} transition-all">{{ $category->name }}</a>
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
                        <span class="text-[10px] font-normal uppercase tracking-widest text-primary">{{ $service->serviceCategory->name ?? $service->category }}</span>
                        <div class="flex items-center gap-1">
                            <i data-lucide="star" class="w-3 h-3 text-amber-400 fill-current"></i>
                            <span class="text-xs font-normal text-gray-400">{{ number_format($service->reviews_avg_rating ?? 5.0, 1) }}</span>
                        </div>
                    </div>
                    <h3 class="text-lg font-normal text-gray-900 mb-2 group-hover:text-primary transition-colors">{{ $service->name }}</h3>
                    <p class="text-xs text-gray-500 leading-relaxed mb-6 flex-1">{{ Str::limit(strip_tags($service->description), 100) }}</p>
                    
                    @php
                        $providerService = null;
                        if(Auth::user()->role === 'provider' && $service->providerServices->isNotEmpty()) {
                            $providerService = $service->providerServices->first();
                        }
                    @endphp

                    @if($providerService)
                        <a href="{{ route('explore.show', $service->id) }}" 
                           class="w-full py-2.5 rounded-lg text-sm font-normal bg-gray-900 text-white hover:bg-primary transition-all duration-300 flex flex-col items-center justify-center">
                            <div class="flex items-center gap-2">
                                <span x-text="t('common.update')"></span>
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </div>
                            <div class="flex items-center gap-2 text-[10px] opacity-70 mt-1">
                                <span>{{ $providerService->projects_count }} <span x-text="t('common.projects')"></span></span>
                                <span>•</span>
                                <span>{{ number_format($providerService->price, 0) }} <span x-text="t('common.sar')"></span></span>
                                <span>•</span>
                                <span>{{ $providerService->delivery_time_days }} <span x-text="t('explore.days')"></span></span>
                            </div>
                        </a>
                    @else
                        <a href="{{ route('explore.show', $service->id) }}" 
                           class="w-full py-3 rounded-lg text-sm font-normal bg-primary-light text-primary hover:bg-gray-900 hover:text-white transition-all duration-300 text-center flex items-center justify-center gap-2">
                            <span x-text="'{{ Auth::user()->role }}' === 'provider' ? t('common.opt_in') : t('common.request')"></span>
                            <i data-lucide="arrow-right" class="w-4 h-4 flip-rtl"></i>
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center bg-gray-50 rounded-3xl border border-dashed border-gray-200">
                <i data-lucide="search-x" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
                <h3 class="text-lg font-normal text-gray-900" x-text="lang === 'ar' ? 'لا توجد نتائج' : 'No services found'"></h3>
                <p class="text-gray-500" x-text="lang === 'ar' ? 'جرب البحث بكلمات أخرى.' : 'Try searching for something else.'"></p>
            </div>
        @endforelse
    </div>
</div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    [dir="rtl"] .flip-rtl { transform: scaleX(-1); }

    ::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    [dir="rtl"] .flip-rtl { transform: scaleX(-1); }
</style>
@endsection
