@extends('layouts.app')

@section('content')
<div class="max-w-7xl w-full p-4 mx-auto">
    <!-- Header -->
    <div class="mb-10 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <h1 class="text-4xl font-black text-gray-900 tracking-tight" x-text="lang === 'ar' ? 'استكشاف الخدمات' : 'Explore Services'"></h1>
        <p class="text-gray-500 text-lg mt-2 font-medium" x-text="lang === 'ar' ? 'اكتشف وتواصل مع أفضل مزودي خدمات الأعمال الموثقين في المملكة.' : 'Discover and connect with Saudi Arabia\'s top verified B2B service providers.'"></p>
    </div>

    <!-- Search Bar -->
    <div class="relative mb-10 animate-in fade-in slide-in-from-bottom-4 duration-700 delay-100">
        <i data-lucide="search" class="w-6 h-6 absolute inset-y-1/2 -translate-y-1/2 start-5 text-gray-400"></i>
        <input type="text" :placeholder="lang === 'ar' ? 'ابحث عن الخدمات أو المزودين...' : 'Search services, providers, or categories...'" 
               class="w-full ps-14 pe-6 py-5 rounded-xl border border-gray-100 bg-white text-gray-700 focus:outline-none focus:ring-4 focus:ring-blue-50 shadow-xl shadow-blue-50/50 placeholder-gray-400 transition-all text-lg font-medium">
    </div>

    <!-- Filter Pills -->
    <div class="flex overflow-x-auto pb-4 mb-8 gap-3 animate-in fade-in slide-in-from-bottom-4 duration-700 delay-200 no-scrollbar">
        <a href="{{ route('explore.index') }}" 
           class="whitespace-nowrap px-6 py-2.5 rounded-[0.5rem] text-sm font-bold {{ !request('category') ? 'bg-gray-900 text-white shadow-lg shadow-gray-200' : 'bg-white text-gray-500 border border-gray-100 hover:border-primary/20 hover:text-primary hover:bg-primary-light' }} transition-all" 
           x-text="lang === 'ar' ? 'جميع الخدمات' : 'All Services'"></a>
        @foreach($categories as $category)
            <a href="{{ route('explore.index', ['category' => $category->slug]) }}" 
               class="whitespace-nowrap px-6 py-2.5 rounded-[0.5rem] text-sm font-bold {{ request('category') === $category->slug ? 'bg-gray-900 text-white shadow-lg shadow-gray-200' : 'bg-white text-gray-500 border border-gray-100 hover:border-primary/20 hover:text-primary hover:bg-primary-light' }} transition-all">{{ $category->name }}</a>
        @endforeach
    </div>

    <!-- Package Upgrade Options -->
    <div class="mb-16 grid grid-cols-1 md:grid-cols-3 gap-8 animate-in fade-in slide-in-from-bottom-4 duration-700 delay-300">
        @if(auth()->user()->role === 'provider')
        <!-- Provider Basic -->
        <div class="bg-white border border-gray-100 rounded-lg p-8 shadow-sm hover:shadow-md transition-all flex flex-col">
            <div class="mb-6">
                <h3 class="text-lg font-black text-gray-900" x-text="lang === 'ar' ? 'الأساسية' : 'Basic'"></h3>
                <p class="text-sm text-gray-400 font-medium" x-text="lang === 'ar' ? 'للمزودين المستقلين' : 'Freelancer start'"></p>
            </div>
            <div class="mb-8 flex items-baseline gap-1">
                <span class="text-4xl font-black text-gray-900">0</span>
                <span class="text-gray-400 font-bold text-xs uppercase" x-text="lang === 'ar' ? 'ر.س / شهر' : 'SAR / month'"></span>
            </div>
            <ul class="space-y-4 mb-10 flex-1">
                <li class="flex items-center text-sm font-medium text-gray-600 gap-3">
                    <i data-lucide="check" class="w-4 h-4 text-green-500"></i>
                    <span x-text="lang === 'ar' ? 'خدمة واحدة' : '1 Service Offering'"></span>
                </li>
            </ul>
            <button class="w-full py-3 bg-gray-50 text-gray-900 rounded-lg font-bold text-sm" x-text="lang === 'ar' ? 'الخطة الحالية' : 'Current Plan'"></button>
        </div>

        <!-- Provider Professional -->
        <div class="bg-white border-2 border-primary rounded-lg p-8 shadow-xl shadow-primary/5 relative flex flex-col">
            <div class="absolute -top-4 inset-x-1/2 -translate-x-1/2 px-4 py-1 bg-primary text-white rounded-full text-[10px] font-black uppercase tracking-widest whitespace-nowrap" x-text="lang === 'ar' ? 'نمو' : 'Growth'" style="width:80px"></div>
            <div class="mb-6">
                <h3 class="text-lg font-black text-gray-900" x-text="lang === 'ar' ? 'الاحترافية' : 'Professional'"></h3>
                <p class="text-sm text-gray-400 font-medium" x-text="lang === 'ar' ? 'للوكالات الصغيرة' : 'Small Agency'"></p>
            </div>
            <div class="mb-8 flex items-baseline gap-1">
                <span class="text-4xl font-black text-gray-900">1,500</span>
                <span class="text-gray-400 font-bold text-xs uppercase" x-text="lang === 'ar' ? 'ر.س / شهر' : 'SAR / month'"></span>
            </div>
            <ul class="space-y-4 mb-10 flex-1">
                <li class="flex items-center text-sm font-medium text-gray-600 gap-3">
                    <i data-lucide="check" class="w-4 h-4 text-green-500"></i>
                    <span x-text="lang === 'ar' ? '3 خدمات' : '3 Service Offerings'"></span>
                </li>
            </ul>
            <button class="w-full py-3 bg-primary text-white rounded-lg font-bold text-sm" x-text="lang === 'ar' ? 'ترقية الآن' : 'Upgrade Now'"></button>
        </div>

        <!-- Provider Enterprise -->
        <div class="bg-blue-900 border border-gray-800 rounded-lg p-8 shadow-sm flex flex-col text-white">
            <div class="mb-6">
                <h3 class="text-lg font-black text-white" x-text="lang === 'ar' ? 'الشركات' : 'Enterprise'"></h3>
                <p class="text-sm text-gray-400 font-medium" x-text="lang === 'ar' ? 'للوكالات الكبيرة' : 'Large Agency'"></p>
            </div>
            <div class="mb-8 flex items-baseline">
                <span class="text-4xl font-black text-white" x-text="lang === 'ar' ? 'مخصص' : 'Custom'"></span>
            </div>
            <ul class="space-y-4 mb-10 flex-1">
                <li class="flex items-center text-sm font-medium text-gray-300 gap-3">
                    <i data-lucide="check" class="w-4 h-4 text-primary"></i>
                    <span x-text="lang === 'ar' ? 'خدمات غير محدودة' : 'Unlimited Services'"></span>
                </li>
            </ul>
            <button class="w-full py-3 bg-white/10 text-white rounded-lg font-bold text-sm" x-text="lang === 'ar' ? 'تواصل مع المبيعات' : 'Contact Sales'"></button>
        </div>
        @else
        <!-- Client Basic -->
        <div class="bg-white border border-gray-100 rounded-lg p-8 shadow-sm hover:shadow-md transition-all flex flex-col">
            <div class="mb-6">
                <h3 class="text-lg font-black text-gray-900" x-text="lang === 'ar' ? 'الأساسية' : 'Basic'"></h3>
                <p class="text-sm text-gray-400 font-medium" x-text="lang === 'ar' ? 'للشركات الصغيرة' : 'Small business'"></p>
            </div>
            <div class="mb-8 flex items-baseline gap-1">
                <span class="text-4xl font-black text-gray-900">0</span>
                <span class="text-gray-400 font-bold text-xs uppercase" x-text="lang === 'ar' ? 'ر.س / شهر' : 'SAR / month'"></span>
            </div>
            <ul class="space-y-4 mb-10 flex-1">
                <li class="flex items-center text-sm font-medium text-gray-600 gap-3">
                    <i data-lucide="check" class="w-4 h-4 text-green-500"></i>
                    <span x-text="lang === 'ar' ? 'طلب خدمة واحدة' : '1 Service Request'"></span>
                </li>
            </ul>
            <button class="w-full py-3 bg-gray-50 text-gray-900 rounded-lg font-bold text-sm" x-text="lang === 'ar' ? 'الخطة الحالية' : 'Current Plan'"></button>
        </div>

        <!-- Client Professional -->
        <div class="bg-white border-2 border-primary rounded-lg p-8 shadow-xl shadow-primary/5 relative flex flex-col">
            <div class="absolute -top-4 inset-x-1/2 -translate-x-1/2 px-4 py-1 bg-primary text-white rounded-full text-[10px] font-black uppercase tracking-widest whitespace-nowrap" x-text="lang === 'ar' ? 'الأكثر شيوعاً' : 'Most Popular'"></div>
            <div class="mb-6">
                <h3 class="text-lg font-black text-gray-900" x-text="lang === 'ar' ? 'الاحترافية' : 'Professional'"></h3>
                <p class="text-sm text-gray-400 font-medium" x-text="lang === 'ar' ? 'للشركات المتنامية' : 'Growing companies'"></p>
            </div>
            <div class="mb-8 flex items-baseline gap-1">
                <span class="text-4xl font-black text-gray-900">2,500</span>
                <span class="text-gray-400 font-bold text-xs uppercase" x-text="lang === 'ar' ? 'ر.س / شهر' : 'SAR / month'"></span>
            </div>
            <ul class="space-y-4 mb-10 flex-1">
                <li class="flex items-center text-sm font-medium text-gray-600 gap-3">
                    <i data-lucide="check" class="w-4 h-4 text-green-500"></i>
                    <span x-text="lang === 'ar' ? '3 طلبات خدمات' : '3 Service Requests'"></span>
                </li>
            </ul>
            <button class="w-full py-3 bg-primary text-white rounded-lg font-bold text-sm" x-text="lang === 'ar' ? 'ترقية الآن' : 'Upgrade Now'"></button>
        </div>

        <!-- Client Enterprise -->
        <div class="bg-gray-900 border border-gray-800 rounded-lg p-8 shadow-sm flex flex-col text-white">
            <div class="mb-6">
                <h3 class="text-lg font-black text-white" x-text="lang === 'ar' ? 'الشركات' : 'Enterprise'"></h3>
                <p class="text-sm text-gray-400 font-medium" x-text="lang === 'ar' ? 'للمنظمات الكبيرة' : 'Large organizations'"></p>
            </div>
            <div class="mb-8 flex items-baseline gap-1">
                <span class="text-4xl font-black text-white" x-text="lang === 'ar' ? 'مخصص' : 'Custom'"></span>
            </div>
            <ul class="space-y-4 mb-10 flex-1">
                <li class="flex items-center text-sm font-medium text-gray-300 gap-3">
                    <i data-lucide="check" class="w-4 h-4 text-primary"></i>
                    <span x-text="lang === 'ar' ? 'طلبات غير محدودة' : 'Unlimited Requests'"></span>
                </li>
            </ul>
            <button class="w-full py-3 bg-white/10 text-white rounded-lg font-bold text-sm" x-text="lang === 'ar' ? 'تواصل مع المبيعات' : 'Contact Sales'"></button>
        </div>
        @endif
    </div>

    <!-- Service Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 animate-in fade-in slide-in-from-bottom-4 duration-700 delay-400">
        @foreach($services as $service)
            <div class="group bg-white rounded-lg border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-500 flex flex-col overflow-hidden relative">
                <div class="h-32 bg-gray-50 relative flex items-center justify-center transition-all group-hover:h-36 overflow-hidden">
                    <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center text-white shadow-xl group-hover:scale-110 transition-all duration-500 z-10">
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
                    <p class="text-xs text-gray-500 leading-relaxed mb-6 flex-1">{{ $service->description }}</p>
                    
                    <a href="{{ route('explore.show', $service->id) }}" 
                       class="w-full py-3 rounded-lg text-sm font-bold bg-primary-light text-primary hover:bg-gray-900 hover:text-white transition-all duration-300 text-center flex items-center justify-center gap-2">
                        <span x-text="'{{ Auth::user()->role }}' === 'provider' ? (lang === 'ar' ? 'انضمام' : 'Opt-in') : (lang === 'ar' ? 'طلب' : 'Request')"></span>
                        <i data-lucide="arrow-right" class="w-4 h-4 flip-rtl"></i>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    [dir="rtl"] .flip-rtl { transform: scaleX(-1); }
</style>
@endsection
