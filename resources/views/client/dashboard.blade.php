@extends('layouts.app')

@section('content')
<div class="max-w-7xl w-full space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-normal text-gray-900">
                <span x-text="t('common.welcome_back')"></span>, {{ Auth::user()->name }}
            </h1>
            <p class="text-gray-500 mt-1" x-text="t('common.manage_projects_subtitle')"></p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('explore.index') }}" class="px-5 py-2.5 bg-primary text-white rounded-lg text-sm font-normal hover:bg-primary-dark transition-all flex items-center gap-2 shadow-lg shadow-primary/20">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span x-text="t('common.new_service_request')"></span>
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @php
            $stats = [
                ['label' => 'common.active_projects', 'value' => $ongoingProjects->count(), 'icon' => 'activity', 'color' => 'text-blue-600', 'bg' => 'bg-blue-50'],
                ['label' => 'common.total_spent', 'value' => number_format($totalSpent, 2), 'icon' => 'credit-card', 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-50'],
                ['label' => 'common.subscribed_services', 'value' => $subscribedServicesCount, 'icon' => 'package', 'color' => 'text-purple-600', 'bg' => 'bg-purple-50'],
            ];
        @endphp

        @foreach($stats as $stat)
        <div class="bg-white p-6 border border-gray-100 rounded-2xl shadow-sm hover:shadow-md transition-all">
            <div class="w-12 h-12 {{ $stat['bg'] }} {{ $stat['color'] }} rounded-xl flex items-center justify-center mb-4">
                <i data-lucide="{{ $stat['icon'] }}" class="w-6 h-6"></i>
            </div>
            <h3 class="text-3xl font-normal text-gray-900">
                {{ $stat['value'] }} @if($stat['label'] === 'common.total_spent') <span class="text-sm" x-text="t('common.sar')"></span> @endif
            </h3>
            <p class="text-gray-400 text-sm font-normal mt-1 uppercase tracking-wider" x-text="t('{{ $stat['label'] }}')"></p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Active Projects List -->
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-normal text-gray-900" x-text="t('common.ongoing_projects')"></h3>
                <a href="#" class="text-xs font-normal text-primary hover:underline" x-text="t('common.view_all')"></a>
            </div>
            
            <div class="grid grid-cols-1 gap-4">
                @forelse($ongoingProjects as $p)
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-primary-light rounded-xl flex items-center justify-center text-primary">
                            <i data-lucide="{{ $p->service->icon }}" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h4 class="font-normal text-gray-900">{{ $p->service->name }}</h4>
                            <p class="text-xs text-gray-400 font-normal">{{ $p->provider->providerProfile->company_name ?? 'iGate Partner' }}</p>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-24 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-primary" style="width: {{ $p->progress }}%"></div>
                            </div>
                            <span class="text-[10px] font-normal text-gray-400">{{ $p->progress }}%</span>
                        </div>
                        <a href="{{ route('projects.show', $p->id) }}" class="text-xs font-normal text-primary hover:underline" x-text="t('common.track_progress')"></a>
                    </div>
                </div>
                @empty
                <div class="py-20 text-center bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
                    <i data-lucide="plus-circle" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
                    <h4 class="text-xl font-normal text-gray-900" x-text="t('common.no_active_projects')"></h4>
                    <p class="text-gray-500 mt-2" x-text="t('common.explore_services_start')"></p>
                </div>
                @endforelse
            </div>

            <!-- Pre-sale Consultations -->
            @if($preSaleChats->count() > 0)
            <div class="mt-12 space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-normal text-gray-900" x-text="t('common.pre_sale_chats')"></h3>
                </div>
                
                <div class="grid grid-cols-1 gap-4">
                    @foreach($preSaleChats as $chat)
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600">
                                <i data-lucide="{{ $chat->service->icon }}" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h4 class="font-normal text-gray-900">{{ $chat->service->name }}</h4>
                                <p class="text-xs text-gray-400 font-normal">{{ $chat->provider->providerProfile->company_name ?? 'iGate Partner' }}</p>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="flex items-center gap-2 mb-1 justify-end">
                                <span class="px-2 py-0.5 bg-amber-50 text-amber-600 rounded-full text-[10px] font-normal" x-text="t('common.consultation')"></span>
                            </div>
                            <a href="{{ route('explore.chat', ['serviceId' => $chat->service_id, 'providerId' => $chat->provider_id]) }}" class="text-xs font-normal text-primary hover:underline" x-text="t('common.chat_now')"></a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Quick Explore Sidebar -->
        @if($settings->recommended_services_enabled)
        <div class="rounded-3xl p-8 text-white" style="background-color: {{ $settings->recommended_services_bg_color ?? '#111827' }}; color: {{ $settings->recommended_services_text_color ?? 'white' }}">
            <h3 class="{{ $settings->recommended_services_heading_size ?? 'text-xl' }} {{ $settings->recommended_services_heading_weight ?? 'font-normal' }} mb-6"
                style="color: {{ $settings->recommended_services_text_color ?? 'white' }}">
                {{ $settings->recommended_services_title ?? __('common.recommended_services') }}
            </h3>
            <div class="space-y-4">
                @foreach($settings->recommended_services_items as $item)
                <a href="{{ $item['link'] ?? '#' }}" class="block p-4 rounded-2xl transition-all cursor-pointer group border border-white/10"
                   style="background-color: {{ $settings->recommended_services_item_bg_color ?? 'rgba(255,255,255,0.05)' }};">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-normal uppercase tracking-widest" style="color: {{ $settings->recommended_services_item_text_color ?? 'var(--primary)' }}">{{ $item['title'] }}</span>
                        <i data-lucide="{{ $item['icon'] ?? 'arrow-right' }}" 
                           class="w-{{ $settings->recommended_services_item_icon_size ?? '4' }} h-{{ $settings->recommended_services_item_icon_size ?? '4' }} group-hover:translate-x-1 transition-transform rtl:group-hover:-translate-x-1"
                           style="color: {{ $settings->recommended_services_item_icon_color ?? 'white' }}"></i>
                    </div>
                    <p class="text-[10px] leading-relaxed" style="color: {{ $settings->recommended_services_item_desc_color ?? '#9CA3AF' }}">{{ $item['description'] }}</p>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Team Task Manager -->
        <div class="bg-white border border-gray-100 rounded-[2.5rem] p-8 shadow-sm">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-lg font-normal" x-text="t('common.messages')"></h3>
            </div>
            <div class="space-y-4">
                <a href="{{ route('internal-messages.index') }}" class="block p-4 bg-gray-50 rounded-2xl border border-gray-100 hover:border-primary/20 hover:bg-primary-light transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-primary shadow-sm group-hover:scale-110 transition-transform">
                            <i data-lucide="check-square" class="w-4 h-4"></i>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs font-normal text-gray-900 truncate" x-text="t('common.messages')"></h4>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        
    </div>
</div>
@endsection
