@extends('layouts.app')

@section('content')
<div class="max-w-7xl w-full space-y-8 animate-in fade-in duration-700">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap:4">
        <div>
            <h1 class="text-2xl font-normal text-gray-900" x-text="t('common.my_services')"></h1>
            <p class="text-gray-500 mt-1" x-text="t('common.manage_pricing_scope')"></p>
        </div>
        <button @click="addServiceOpen = true" class="px-6 py-3 bg-primary text-white rounded-xl font-normal hover:bg-primary-dark transition-all flex items-center gap-2 shadow-lg shadow-primary/20">
            <i data-lucide="plus" class="w-5 h-5"></i>
            <span x-text="t('explore.add_new_service')"></span>
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- My Active Offerings -->
        <div class="lg:col-span-2 space-y-6">
            <h2 class="text-lg font-normal text-gray-900 flex items-center gap-2">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-500"></i>
                <span x-text="t('common.active_offerings')"></span>
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($myServices as $ps)
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all flex flex-col" x-data="{ activeTab: null }">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-primary-light rounded-xl flex items-center justify-center text-primary">
                                <i data-lucide="{{ $ps->service->icon }}" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="font-normal text-gray-900 text-lg">{{ $ps->service->name }}</h3>
                                <span class="text-[10px] font-normal uppercase text-gray-400 tracking-widest">{{ $ps->service->category }}</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('explore.show', $ps->service->id) }}" class="p-2 text-gray-400 hover:text-primary transition-colors">
                                <i data-lucide="edit-3" class="w-5 h-5"></i>
                            </a>
                            <form action="{{ route('provider.services.destroy', $ps->id) }}" method="POST" onsubmit="return confirm('{{ __('common.are_you_sure') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-500 transition-colors">
                                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100 mb-6">
                        <div>
                            <span class="block text-[10px] font-normal text-gray-400 uppercase tracking-widest mb-1" x-text="t('common.status')"></span>
                            <span class="text-xs font-normal uppercase {{ $ps->is_active ? 'text-emerald-600' : 'text-red-600' }}" x-text="{{ $ps->is_active ? 'true' : 'false' }} ? t('common.active') : t('common.inactive')"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-normal text-gray-400 uppercase tracking-widest mb-1" x-text="t('common.delivery')"></span>
                            <span class="text-sm font-normal text-gray-900" x-text="t('common.days_count').replace(':count', '{{ $ps->delivery_time_days }}')"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-normal text-gray-400 uppercase tracking-widest mb-1" x-text="t('common.clients')"></span>
                            <span class="text-sm font-normal text-gray-900">{{ $ps->projects_count }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-normal text-gray-400 uppercase tracking-widest mb-1" x-text="t('common.rating')"></span>
                            <span class="flex items-center text-sm font-normal text-amber-500">
                                <i data-lucide="star" class="w-3 h-3 fill-current mr-1"></i> 5.0
                            </span>
                        </div>
                    </div>

                    <!-- Action Tabs -->
                    <div class="flex border-t border-gray-50 pt-4 gap-2 mt-auto">
                        <button @click="activeTab = activeTab === 'projects' ? null : 'projects'" 
                                :class="activeTab === 'projects' ? 'bg-primary text-white' : 'bg-gray-50 text-gray-600 hover:bg-gray-100'"
                                class="flex-1 px-4 py-2.5 rounded-xl text-xs font-normal transition-all flex items-center justify-center gap-2">
                            <i data-lucide="layers" class="w-4 h-4"></i>
                            <span x-text="t('common.projects')"></span>
                        </button>
                        <button @click="activeTab = activeTab === 'chats' ? null : 'chats'" 
                                :class="activeTab === 'chats' ? 'bg-primary text-white' : 'bg-gray-50 text-gray-600 hover:bg-gray-100'"
                                class="flex-1 px-4 py-2.5 rounded-xl text-xs font-normal transition-all flex items-center justify-center gap-2">
                            <i data-lucide="message-square" class="w-4 h-4"></i>
                            <span x-text="t('common.pre_sale_chats')"></span>
                        </button>
                    </div>

                    <!-- Dropdown Content -->
                    <div x-show="activeTab" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="mt-4 space-y-3 pt-4 border-t border-dashed border-gray-100">
                        
                        <!-- Projects List -->
                        <template x-if="activeTab === 'projects'">
                            <div class="space-y-2">
                                @forelse($ps->projects as $p)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100 hover:border-primary/20 transition-all group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-primary shadow-sm group-hover:scale-105 transition-transform">
                                            <i data-lucide="briefcase" class="w-4 h-4"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-normal text-gray-900">{{ $p->client->name }}</p>
                                            <p class="text-[9px] font-normal text-gray-400 uppercase tracking-widest">{{ $p->status }}</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('projects.show', $p->id) }}" class="p-1.5 bg-white text-gray-400 hover:text-primary rounded-lg transition-colors">
                                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                                @empty
                                <p class="text-center text-gray-400 text-[10px] font-normal uppercase tracking-widest italic py-4" x-text="t('common.no_active_projects')"></p>
                                @endforelse
                            </div>
                        </template>

                        <!-- Pre-sale Chats List -->
                        <template x-if="activeTab === 'chats'">
                            <div class="space-y-2">
                                @php
                                    $uniqueChats = $ps->preSaleMessages->unique('client_id');
                                @endphp
                                @forelse($uniqueChats as $chat)
                                <a href="{{ route('explore.chat', ['serviceId' => $chat->service_id, 'providerId' => $chat->provider_id, 'client_id' => $chat->client_id]) }}" 
                                   class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100 hover:border-primary/20 transition-all group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-primary shadow-sm group-hover:scale-105 transition-transform">
                                            <i data-lucide="user" class="w-4 h-4"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-normal text-gray-900">{{ $chat->client->name }}</p>
                                            <p class="text-[9px] font-normal text-gray-400 uppercase tracking-widest">{{ $chat->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <div class="p-1.5 bg-white text-gray-400 group-hover:text-primary rounded-lg transition-colors">
                                        <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                                    </div>
                                </a>
                                @empty
                                <p class="text-center text-gray-400 text-[10px] font-normal uppercase tracking-widest italic py-4" x-text="t('common.no_pre_sale_chats_yet')"></p>
                                @endforelse
                            </div>
                        </template>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                    <p class="text-gray-500 font-normal" x-text="t('common.no_services_yet')"></p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Catalog Sidebar -->
        <div class="space-y-6">
            <h2 class="text-lg font-normal text-gray-900" x-text="t('common.standard_catalog')"></h2>
            <div class="bg-gray-900 rounded-3xl p-6 text-white shadow-xl shadow-gray-200">
                <p class="text-xs text-gray-400 font-normal uppercase tracking-widest mb-4" x-text="t('common.why_standardized')"></p>
                <p class="text-sm text-gray-300 leading-relaxed mb-6" x-text="t('common.standardized_explanation')"></p>
                <ul class="space-y-4">
                    <li class="flex items-center gap-3 text-xs font-normal">
                        <i data-lucide="shield-check" class="w-4 h-4 text-primary"></i>
                        <span x-text="t('common.guaranteed_payments')"></span>
                    </li>
                    <li class="flex items-center gap-3 text-xs font-normal">
                        <i data-lucide="clock" class="w-4 h-4 text-primary"></i>
                        <span x-text="t('common.sla_protection')"></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
