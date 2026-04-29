@extends('layouts.app')

@section('content')
<div class="max-w-7xl w-full space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700" x-data="{ lang: localStorage.getItem('igate_lang') || 'en' }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900" x-text="lang === 'ar' ? 'أهلاً بك، ' + '{{ Auth::user()->name }}' : 'Welcome back, ' + '{{ Auth::user()->name }}'"></h1>
            <p class="text-gray-500 mt-1" x-text="lang === 'ar' ? 'إدارة مشاريعك وطلبات الخدمات بكل سهولة.' : 'Manage your projects and service requests seamlessly.'"></p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('explore.index') }}" class="px-5 py-2.5 bg-primary text-white rounded-lg text-sm font-bold hover:bg-primary-dark transition-all flex items-center gap-2 shadow-lg shadow-primary/20">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span x-text="lang === 'ar' ? 'طلب خدمة جديدة' : 'New Service Request'"></span>
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @php
            $stats = [
                ['label' => ['en' => 'Active Projects', 'ar' => 'المشاريع النشطة'], 'value' => $ongoingProjects->count(), 'icon' => 'activity', 'color' => 'text-blue-600', 'bg' => 'bg-blue-50'],
                ['label' => ['en' => 'Total Spent', 'ar' => 'إجمالي المنفق'], 'value' => 'SAR 42,000', 'icon' => 'credit-card', 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-50'],
                ['label' => ['en' => 'Subscribed Services', 'ar' => 'الخدمات المشترك بها'], 'value' => '5', 'icon' => 'package', 'color' => 'text-purple-600', 'bg' => 'bg-purple-50'],
            ];
        @endphp

        @foreach($stats as $stat)
        <div class="bg-white p-6 border border-gray-100 rounded-2xl shadow-sm hover:shadow-md transition-all">
            <div class="w-12 h-12 {{ $stat['bg'] }} {{ $stat['color'] }} rounded-xl flex items-center justify-center mb-4">
                <i data-lucide="{{ $stat['icon'] }}" class="w-6 h-6"></i>
            </div>
            <h3 class="text-3xl font-bold text-gray-900">{{ $stat['value'] }}</h3>
            <p class="text-gray-400 text-sm font-bold mt-1 uppercase tracking-wider" x-text="lang === 'ar' ? '{{ $stat['label']['ar'] }}' : '{{ $stat['label']['en'] }}'"></p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Active Projects List -->
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900" x-text="lang === 'ar' ? 'المشاريع الجارية' : 'Ongoing Projects'"></h3>
                <a href="#" class="text-xs font-bold text-primary hover:underline" x-text="lang === 'ar' ? 'عرض الكل' : 'View All'"></a>
            </div>
            
            <div class="grid grid-cols-1 gap-4">
                @forelse($ongoingProjects as $p)
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-primary-light rounded-xl flex items-center justify-center text-primary">
                            <i data-lucide="{{ $p->service->icon }}" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">{{ $p->service->name }}</h4>
                            <p class="text-xs text-gray-400 font-medium">{{ $p->provider->providerProfile->company_name ?? 'iGate Partner' }}</p>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-24 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-primary w-2/3"></div>
                            </div>
                            <span class="text-[10px] font-bold text-gray-400">65%</span>
                        </div>
                        <a href="{{ route('projects.show', $p->id) }}" class="text-xs font-bold text-primary hover:underline" x-text="lang === 'ar' ? 'عرض التقدم' : 'Track Progress'"></a>
                    </div>
                </div>
                @empty
                <div class="py-20 text-center bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
                    <i data-lucide="plus-circle" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
                    <h4 class="text-xl font-bold text-gray-900" x-text="lang === 'ar' ? 'لا توجد مشاريع نشطة' : 'No active projects'"></h4>
                    <p class="text-gray-500 mt-2" x-text="lang === 'ar' ? 'استكشف الخدمات وابدأ مشروعك الأول اليوم.' : 'Explore services and start your first project today.'"></p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Explore Sidebar -->
        <div class="bg-gray-900 rounded-3xl p-8 text-white">
            <h3 class="text-xl font-bold mb-6" x-text="lang === 'ar' ? 'خدمات مقترحة' : 'Recommended Services'"></h3>
            <div class="space-y-4">
                @foreach(['ZATCA Compliance', 'Legal Review', 'HR Management'] as $rec)
                <div class="p-4 bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10 transition-all cursor-pointer group">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-primary uppercase tracking-widest">{{ $rec }}</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 text-white group-hover:translate-x-1 transition-transform rtl:group-hover:-translate-x-1"></i>
                    </div>
                    <p class="text-[10px] text-gray-400 leading-relaxed" x-text="lang === 'ar' ? 'نظام متكامل لضمان التوافق مع الأنظمة السعودية.' : 'Complete solution for Saudi regulatory compliance.'"></p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
