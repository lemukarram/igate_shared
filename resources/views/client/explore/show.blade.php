@extends('layouts.app')

@section('content')
<div class="max-w-6xl w-full space-y-8 p-4" x-data="{ addServiceModalOpen: false, editServiceModalOpen: false, providerInfoModalOpen: false, selectedProvider: {} }">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ auth()->user()->role === 'provider' ? route('provider.services.index') : route('explore.index') }}" class="w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-900 transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5 flip-rtl"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $service->name }}</h1>
                <p class="text-gray-500">{{ $service->description }}</p>
            </div>
        </div>

        @if(auth()->user()->role === 'provider')
            <div class="flex items-center gap-3">
                @if($providerService)
                    <div class="bg-blue-50 px-4 py-2 rounded-xl border border-blue-100 flex items-center gap-3 me-2">
                        <div class="text-blue-600 font-bold text-xl">{{ $clientCount }}</div>
                        <div class="text-blue-500 text-xs uppercase font-bold leading-tight" x-html="t('common.active_clients_br')"></div>
                    </div>
                    <form action="{{ route('provider.services.destroy', $providerService->id) }}" method="POST" onsubmit="return confirm('{{ __('common.are_you_sure') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-6 py-3 bg-red-50 text-red-600 rounded-xl font-bold hover:bg-red-100 transition-all border border-red-100">
                            <span x-text="t('common.remove_service')"></span>
                        </button>
                    </form>
                @else
                    <button @click="addServiceModalOpen = true" class="px-6 py-3 bg-primary text-white rounded-xl font-bold hover:bg-primary-dark transition-all shadow-sm">
                        <span x-text="t('common.add_to_my_services')"></span>
                    </button>
                @endif
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-sm">
                <h2 class="text-xl font-bold text-gray-900 mb-6" x-text="t('common.service_scope_subtasks')"></h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($service->subtasks)
                        @foreach($service->subtasks as $subtask)
                            <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <div class="mt-1 w-5 h-5 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="check" class="w-3 h-3 text-blue-600"></i>
                                </div>
                                <span class="text-gray-700 font-medium">{{ $subtask }}</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-500 italic" x-text="t('common.no_subtasks')"></p>
                    @endif
                </div>
            </div>

            @if(auth()->user()->role === 'client')
            <div class="space-y-6">
                <h2 class="text-xl font-bold text-gray-900" x-text="t('common.available_providers')"></h2>
                
                <div class="grid grid-cols-1 gap-4">
                    @forelse($providers as $ps)
                    <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all flex flex-col md:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-4 flex-1">
                            <button @click="selectedProvider = { 
                                name: '{{ $ps->provider->providerProfile->company_name ?? $ps->provider->name }}',
                                logo: '{{ $ps->provider->providerProfile && $ps->provider->providerProfile->logo ? asset('storage/' . $ps->provider->providerProfile->logo) : '' }}',
                                totalClients: '{{ rand(50, 200) }}',
                                completedProjects: '{{ rand(150, 500) }}',
                                activeProjects: '{{ rand(5, 20) }}',
                                about: '{{ addslashes($ps->provider->providerProfile->about ?? '') }}'
                            }; providerInfoModalOpen = true" class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center overflow-hidden hover:ring-4 hover:ring-primary/10 transition-all">
                                @if($ps->provider->providerProfile && $ps->provider->providerProfile->logo)
                                    <img src="{{ asset('storage/' . $ps->provider->providerProfile->logo) }}" class="w-full h-full object-cover">
                                @else
                                    <i data-lucide="building-2" class="w-8 h-8 text-gray-400"></i>
                                @endif
                            </button>
                            <div>
                                <button @click="selectedProvider = { 
                                    name: '{{ $ps->provider->providerProfile->company_name ?? $ps->provider->name }}',
                                    logo: '{{ $ps->provider->providerProfile && $ps->provider->providerProfile->logo ? asset('storage/' . $ps->provider->providerProfile->logo) : '' }}',
                                    totalClients: '{{ rand(50, 200) }}',
                                    completedProjects: '{{ rand(150, 500) }}',
                                    activeProjects: '{{ rand(5, 20) }}',
                                    about: '{{ addslashes($ps->provider->providerProfile->about ?? '') }}'
                                }; providerInfoModalOpen = true" class="text-lg font-bold text-gray-900 hover:text-primary transition-colors text-left">
                                    {{ $ps->provider->providerProfile->company_name ?? $ps->provider->name }}
                                </button>
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <span class="flex items-center"><i data-lucide="star" class="w-4 h-4 text-yellow-400 fill-current me-1"></i> 5.0</span>
                                    <span>•</span>
                                    <span x-text="t('common.verified_provider')"></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-12 px-8 border-x border-gray-50 hidden lg:flex">
                            <div class="text-center">
                                <span class="block text-xs text-gray-400 uppercase font-semibold" x-text="t('common.delivery')"></span>
                                <span class="text-sm font-bold text-gray-900" x-text="t('common.days_count').replace(':count', '{{ $ps->delivery_time_days }}')"></span>
                            </div>
                            <div class="text-center">
                                <span class="block text-xs text-gray-400 uppercase font-semibold" x-text="t('common.price')"></span>
                                <span class="text-xl font-black text-blue-600">{{ number_format($ps->price, 0) }} <span x-text="t('common.sar')"></span></span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 w-full md:w-auto">
                            <a href="{{ route('explore.chat', ['serviceId' => $service->id, 'providerId' => $ps->provider_id]) }}" 
                               class="flex-1 md:flex-none px-6 py-3 bg-gray-50 text-gray-700 rounded-xl font-bold hover:bg-gray-100 transition-all border border-gray-100 text-center">
                                <span x-text="t('common.chat')"></span>
                            </a>
                            <a href="{{ route('checkout.review', $ps->id) }}" class="flex-1 md:flex-none px-8 py-3 bg-primary text-white rounded-lg font-bold hover:bg-primary-dark transition-all shadow-sm text-center">
                                <span x-text="t('common.request')"></span>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="py-20 text-center bg-gray-50 rounded-3xl border border-dashed border-gray-200">
                        <i data-lucide="users-2" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-900" x-text="t('common.no_providers_yet')"></h3>
                        <p class="text-gray-500" x-text="t('common.no_providers_subtitle')"></p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            @if(auth()->user()->role === 'provider' && $providerService)
            <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900" x-text="t('common.my_offering_details')"></h2>
                    <button @click="editServiceModalOpen = true" class="px-4 py-2 bg-gray-900 text-white rounded-xl text-sm font-bold hover:bg-gray-800 transition-all flex items-center gap-2">
                        <i data-lucide="edit-3" class="w-4 h-4"></i> <span x-text="t('common.edit_details')"></span>
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div class="p-6 bg-blue-50 rounded-2xl border border-blue-100">
                        <span class="block text-xs text-blue-500 uppercase font-black tracking-widest mb-1" x-text="t('common.my_price')"></span>
                        <span class="text-3xl font-black text-blue-700">{{ number_format($providerService->price, 0) }} <span class="text-sm" x-text="t('common.sar')"></span></span>
                    </div>
                    <div class="p-6 bg-indigo-50 rounded-2xl border border-indigo-100">
                        <span class="block text-xs text-indigo-500 uppercase font-black tracking-widest mb-1" x-text="t('common.delivery')"></span>
                        <span class="text-3xl font-black text-indigo-700" x-text="t('common.days_count').replace(':count', '{{ $providerService->delivery_time_days }}')"></span>
                    </div>
                </div>

                <div>
                    <span class="block text-xs text-gray-400 uppercase font-black tracking-widest mb-3" x-text="t('common.service_notes_terms')"></span>
                    <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100 text-gray-700 leading-relaxed italic">
                        {{ $providerService->provider_notes ?? __('common.no_specific_notes') }}
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Standardized Sidebar -->
        <div class="space-y-6">
            <h2 class="text-lg font-bold text-gray-900" x-text="t('common.standard_catalog')"></h2>
            <div class="bg-gray-900 rounded-3xl p-6 text-white shadow-xl shadow-gray-200">
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-4" x-text="t('common.why_standardized')"></p>
                <p class="text-sm text-gray-300 leading-relaxed mb-6" x-text="t('common.standardized_explanation')"></p>
                <ul class="space-y-4">
                    <li class="flex items-center gap-3 text-xs font-bold">
                        <i data-lucide="shield-check" class="w-4 h-4 text-primary"></i>
                        <span x-text="t('common.guaranteed_payments')"></span>
                    </li>
                    <li class="flex items-center gap-3 text-xs font-bold">
                        <i data-lucide="clock" class="w-4 h-4 text-primary"></i>
                        <span x-text="t('common.sla_protection')"></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- MODALS -->
    <!-- Provider Info Modal -->
    <div x-show="providerInfoModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center" style="display: none;">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-md" @click="providerInfoModalOpen = false"></div>
        <div class="bg-white w-full max-w-2xl rounded-[2.5rem] shadow-2xl relative z-10 p-0 border border-gray-100 animate-in zoom-in duration-300 max-h-[90vh] overflow-y-auto custom-scrollbar">
            <div class="relative h-32 bg-primary/10">
                <button @click="providerInfoModalOpen = false" class="absolute top-6 right-6 p-2 bg-white/20 hover:bg-white/40 rounded-full text-gray-900 backdrop-blur-md transition-all">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="px-10 pb-10 -mt-12">
                <div class="flex items-end justify-between mb-8">
                    <div class="w-24 h-24 bg-white rounded-3xl p-1 shadow-xl">
                        <template x-if="selectedProvider.logo">
                            <img :src="selectedProvider.logo" class="w-full h-full rounded-[1.25rem] object-cover">
                        </template>
                        <template x-if="!selectedProvider.logo">
                            <div class="w-full h-full bg-gray-50 rounded-[1.25rem] flex items-center justify-center text-gray-300 border-2 border-dashed border-gray-100">
                                <i data-lucide="building-2" class="w-10 h-10"></i>
                            </div>
                        </template>
                    </div>
                    <div class="flex gap-2">
                        <span class="px-4 py-1.5 bg-green-50 text-green-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-green-100" x-text="t('common.verified_expert')"></span>
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-3xl font-black text-gray-900 mb-2" x-text="selectedProvider.name"></h2>
                    <p class="text-gray-500 text-sm font-medium leading-relaxed" x-text="selectedProvider.about || 'No company bio provided yet.'"></p>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-8">
                    <div class="bg-gray-50 p-6 rounded-3xl border border-gray-100 text-center">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1" x-text="t('common.total_clients')"></p>
                        <p class="text-2xl font-black text-gray-900" x-text="selectedProvider.totalClients"></p>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-3xl border border-gray-100 text-center">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1" x-text="t('common.completed_projects')"></p>
                        <p class="text-2xl font-black text-green-600" x-text="selectedProvider.completedProjects"></p>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-3xl border border-gray-100 text-center">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1" x-text="t('common.active_projects')"></p>
                        <p class="text-2xl font-black text-primary" x-text="selectedProvider.activeProjects"></p>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-black text-gray-900 flex items-center gap-2">
                        <i data-lucide="message-square-quote" class="w-5 h-5 text-primary"></i>
                        <span x-text="t('common.reviews')"></span>
                    </h3>
                    <div class="space-y-4">
                        <template x-for="i in 2" :key="i">
                            <div class="p-6 bg-white border border-gray-100 rounded-3xl shadow-sm">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-[10px] font-black uppercase text-gray-400">CL</div>
                                        <div>
                                            <p class="text-xs font-bold text-gray-900">Verified Client</p>
                                            <p class="text-[10px] text-gray-400 font-medium">Project ID: 49210</p>
                                        </div>
                                    </div>
                                    <div class="flex text-yellow-400"><i data-lucide="star" class="w-3 h-3 fill-current"></i><i data-lucide="star" class="w-3 h-3 fill-current"></i><i data-lucide="star" class="w-3 h-3 fill-current"></i><i data-lucide="star" class="w-3 h-3 fill-current"></i><i data-lucide="star" class="w-3 h-3 fill-current"></i></div>
                                </div>
                                <p class="text-xs text-gray-600 leading-relaxed font-medium italic">"Highly professional delivery. The standardized scope really helped us understand exactly what we were getting. Completed 2 days ahead of schedule!"</p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODALS -->
    @if(auth()->user()->role === 'provider')
    <!-- Add Service Modal -->
    <div x-show="addServiceModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center" style="display: none;">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="addServiceModalOpen = false"></div>
        <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl relative z-10 p-10 border border-gray-100 animate-in zoom-in duration-300">
            <div class="flex items-center justify-between mb-8"><h2 class="text-2xl font-bold" x-text="t('explore.add_service')"></h2><button @click="addServiceModalOpen = false" class="text-gray-400 hover:text-gray-600"><i data-lucide="x" class="w-6 h-6"></i></button></div>
            <form action="{{ route('provider.services.store') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="service_id" value="{{ $service->id }}">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1"><label class="text-[10px] font-black uppercase tracking-widest text-gray-400" x-text="t('explore.price')"></label><input type="number" name="price" step="0.01" required class="w-full px-4 py-3 border border-gray-100 bg-gray-50 rounded-xl text-sm font-bold"></div>
                    <div class="space-y-1"><label class="text-[10px] font-black uppercase tracking-widest text-gray-400" x-text="t('explore.days')"></label><input type="number" name="delivery_time_days" required class="w-full px-4 py-3 border border-gray-100 bg-gray-50 rounded-xl text-sm font-bold"></div>
                </div>
                <button type="submit" class="w-full py-4 bg-primary text-white rounded-xl font-bold" x-text="t('explore.add_to_portfolio_btn')"></button>
            </form>
        </div>
    </div>

    <!-- Edit Service Modal -->
    @if($providerService)
    <div x-show="editServiceModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center" style="display: none;">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="editServiceModalOpen = false"></div>
        <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl relative z-10 p-10 border border-gray-100 animate-in zoom-in duration-300">
            <div class="flex items-center justify-between mb-8"><h2 class="text-2xl font-bold" x-text="t('common.edit_service')"></h2><button @click="editServiceModalOpen = false" class="text-gray-400 hover:text-gray-600"><i data-lucide="x" class="w-6 h-6"></i></button></div>
            <form action="{{ route('provider.services.update', $providerService->id) }}" method="POST" class="space-y-6">
                @csrf @method('PATCH')
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1"><label class="text-[10px] font-black uppercase tracking-widest text-gray-400" x-text="t('common.price')"></label><input type="number" name="price" step="0.01" value="{{ $providerService->price }}" required class="w-full px-4 py-3 border border-gray-100 bg-gray-50 rounded-xl text-sm font-bold"></div>
                    <div class="space-y-1"><label class="text-[10px] font-black uppercase tracking-widest text-gray-400" x-text="t('common.days')"></label><input type="number" name="delivery_time_days" value="{{ $providerService->delivery_time_days }}" required class="w-full px-4 py-3 border border-gray-100 bg-gray-50 rounded-xl text-sm font-bold"></div>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400" x-text="t('common.additional_notes')"></label>
                    <textarea name="provider_notes" rows="4" class="w-full px-4 py-3 border border-gray-100 bg-gray-50 rounded-xl text-sm font-medium resize-none">{{ $providerService->provider_notes }}</textarea>
                </div>
                <button type="submit" class="w-full py-4 bg-gray-900 text-white rounded-xl font-bold" x-text="t('common.save')"></button>
            </form>
        </div>
    </div>
    @endif
    @endif
</div>

<style>
    [dir="rtl"] .flip-rtl { transform: scaleX(-1); }
</style>
@endsection
