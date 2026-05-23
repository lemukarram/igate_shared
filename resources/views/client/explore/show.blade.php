@extends('layouts.app')

@section('content')
<div class="max-w-6xl w-full space-y-8 p-4" x-data="{ addServiceModalOpen: false, editServiceModalOpen: false, providerInfoModalOpen: false, selectedProvider: {} }">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ auth()->user()->isProviderMode() ? route('provider.services.index') : route('explore.index') }}" class="w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-900 transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5 flip-rtl"></i>
            </a>
            <div>
                <h1 class="text-3xl font-normal text-gray-900">{{ $service->name }}</h1>
                <div class="text-gray-500">{!! $service->description !!}</div>
            </div>
        </div>

        @if(auth()->user()->isProviderMode())
            <div class="flex items-center gap-3">
                @if($providerService)
                    <div class="bg-blue-50 px-4 py-2 rounded-xl border border-blue-100 flex items-center gap-3 me-2">
                        <div class="text-blue-600 font-normal text-xl">{{ $clientCount }}</div>
                        <div class="text-blue-500 text-xs uppercase font-normal leading-tight" x-html="t('common.active_clients_br')"></div>
                    </div>
                    <form action="{{ route('provider.services.destroy', $providerService->id) }}" method="POST" onsubmit="return confirm('{{ __('common.are_you_sure') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-6 py-3 bg-red-50 text-red-600 rounded-xl font-normal hover:bg-red-100 transition-all border border-red-100">
                            <span x-text="t('common.remove_service')"></span>
                        </button>
                    </form>
                @else
                    <button @click="addServiceModalOpen = true" class="px-6 py-3 bg-primary text-white rounded-xl font-normal hover:bg-primary-dark transition-all shadow-sm">
                        <span x-text="t('common.add_to_my_services')"></span>
                    </button>
                @endif
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-sm">
                <h2 class="text-xl font-normal text-gray-900 mb-6" x-text="t('common.service_scope_subtasks')"></h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($service->subtasks)
                        @foreach($service->subtasks as $subtask)
                            <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <div class="mt-1 w-5 h-5 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="check" class="w-3 h-3 text-blue-600"></i>
                                </div>
                                <span class="text-gray-700 font-normal">{{ $subtask }}</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-500 italic" x-text="t('common.no_subtasks')"></p>
                    @endif
                </div>
            </div>

            @if(auth()->user()->isClientMode())
            <div class="space-y-6">
                <h2 class="text-xl font-normal text-gray-900" x-text="t('common.available_providers')"></h2>
                
                <div class="grid grid-cols-1 gap-4">
                    @forelse($providers as $ps)
                    <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all flex flex-col md:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-4 flex-1">
                            <button @click="selectedProvider = { 
                                name: '{{ $ps->provider->providerProfile->company_name ?? $ps->provider->name }}',
                                logo: '{{ $ps->provider->providerProfile && $ps->provider->providerProfile->logo ? asset('storage/' . $ps->provider->providerProfile->logo) : '' }}',
                                totalClients: '{{ $ps->provider->total_clients_count }}',
                                completedProjects: '{{ $ps->provider->completed_projects_count }}',
                                activeProjects: '{{ $ps->provider->active_projects_count }}',
                                about: '{{ addslashes($ps->provider->providerProfile->about ?? '') }}',
                                reviews: {{ $ps->provider->reviewsReceived()->with(['reviewer', 'project.service'])->latest()->take(5)->get()->map(function($r) {
                                    return [
                                        'rating' => $r->rating,
                                        'comment' => $r->comment,
                                        'reviewer_name' => $r->reviewer->name,
                                        'reviewer_logo' => $r->reviewer->profile_picture ? asset('storage/' . $r->reviewer->profile_picture) : null,
                                        'project_name' => $r->project->service->name
                                    ];
                                })->toJson() }},
                                averageRating: '{{ number_format($ps->provider->average_rating, 1) }}'
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
                                    totalClients: '{{ $ps->provider->total_clients_count }}',
                                    completedProjects: '{{ $ps->provider->completed_projects_count }}',
                                    activeProjects: '{{ $ps->provider->active_projects_count }}',
                                    about: '{{ addslashes($ps->provider->providerProfile->about ?? '') }}',
                                    reviews: {{ $ps->provider->reviewsReceived()->with(['reviewer', 'project.service'])->latest()->take(5)->get()->map(function($r) {
                                        return [
                                            'rating' => $r->rating,
                                            'comment' => $r->comment,
                                            'reviewer_name' => $r->reviewer->name,
                                            'reviewer_logo' => $r->reviewer->profile_picture ? asset('storage/' . $r->reviewer->profile_picture) : null,
                                            'project_name' => $r->project->service->name
                                        ];
                                    })->toJson() }},
                                    averageRating: '{{ number_format($ps->provider->average_rating, 1) }}'
                                }; providerInfoModalOpen = true" class="text-lg font-normal text-gray-900 hover:text-primary transition-colors text-left">
                                    {{ $ps->provider->providerProfile->company_name ?? $ps->provider->name }}
                                </button>
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <span class="flex items-center"><i data-lucide="star" class="w-4 h-4 text-yellow-400 fill-current me-1"></i> {{ number_format($ps->provider->average_rating, 1) }}</span>
                                    <span>•</span>
                                    <span x-text="t('common.verified_provider')"></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col lg:flex-row items-center gap-12 px-8 border-x border-gray-50 w-full lg:w-auto py-4 lg:py-0">
                            <div class="flex items-center justify-between w-full lg:w-auto lg:gap-12">
                                <div class="text-center">
                                    <span class="block text-xs text-gray-400 uppercase font-normal" x-text="t('common.delivery')"></span>
                                    <span class="text-sm font-normal text-gray-900" x-text="t('common.days_count').replace(':count', '{{ $ps->delivery_time_days }}')"></span>
                                </div>
                                <div class="text-center">
                                    <span class="block text-xs text-gray-400 uppercase font-normal" x-text="t('common.price')"></span>
                                    <div class="flex flex-col items-center">
                                        @if($ps->annual_price)
                                            <span class="text-xl font-normal text-blue-600">
                                                {{ number_format($ps->annual_price / 12, 0) }} 
                                                <span class="text-xs font-normal text-gray-500" x-text="t('common.sar_month')"></span>
                                            </span>
                                            <span class="text-[10px] text-green-600 font-medium bg-green-50 px-2 py-0.5 rounded-full mt-1 whitespace-nowrap">
                                                {{ $ps->annual_discount_percentage }}% {{ __('common.save_more') }}
                                            </span>
                                        @else
                                            <span class="text-xl font-normal text-blue-600">{{ number_format($ps->monthly_price, 0) }} <span class="text-xs font-normal text-gray-500" x-text="t('common.sar_month')"></span></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 w-full md:w-48">
                            <a href="{{ route('explore.chat', ['serviceId' => $service->id, 'providerId' => $ps->provider_id]) }}" 
                               class="w-full px-6 py-3 bg-gray-50 text-gray-700 rounded-xl font-normal hover:bg-gray-100 transition-all border border-gray-100 text-center">
                                <span x-text="t('common.chat')"></span>
                            </a>
                            @if($ps->existing_project_id)
                                <a href="{{ route('projects.show', $ps->existing_project_id) }}" class="w-full px-8 py-3 bg-gray-900 text-white rounded-lg font-normal hover:bg-black transition-all shadow-sm text-center">
                                    <span>View Project</span>
                                </a>
                            @else
                                <a href="{{ route('checkout.review', $ps->id) }}" class="w-full px-8 py-3 bg-primary text-white rounded-lg font-normal hover:bg-primary-dark transition-all shadow-sm text-center">
                                    <span x-text="t('common.request')"></span>
                                </a>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="py-20 text-center bg-gray-50 rounded-3xl border border-dashed border-gray-200">
                        <i data-lucide="users-2" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
                        <h3 class="text-lg font-normal text-gray-900" x-text="t('common.no_providers_yet')"></h3>
                        <p class="text-gray-500" x-text="t('common.no_providers_subtitle')"></p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            @if(auth()->user()->isProviderMode() && $providerService)
            <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-normal text-gray-900" x-text="t('common.my_offering_details')"></h2>
                    <button @click="editServiceModalOpen = true" class="px-4 py-2 bg-gray-900 text-white rounded-xl text-sm font-normal hover:bg-gray-800 transition-all flex items-center gap-2">
                        <i data-lucide="edit-3" class="w-4 h-4"></i> <span x-text="t('common.edit_details')"></span>
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                    <div class="p-6 bg-blue-50 rounded-2xl border border-blue-100">
                        <span class="block text-xs text-blue-500 uppercase font-normal tracking-widest mb-1" x-text="t('common.my_price')"></span>
                        <span class="text-3xl font-normal text-blue-700">{{ number_format($providerService->monthly_price, 0) }} <span class="text-sm" x-text="t('common.sar')"></span></span>
                    </div>
                    <div class="p-6 bg-green-50 rounded-2xl border border-green-100">
                        <span class="block text-xs text-green-500 uppercase font-normal tracking-widest mb-1">Annual Discount</span>
                        <span class="text-3xl font-normal text-green-700">{{ $providerService->annual_discount_percentage }}%</span>
                    </div>
                    <div class="p-6 bg-indigo-50 rounded-2xl border border-indigo-100">
                        <span class="block text-xs text-indigo-500 uppercase font-normal tracking-widest mb-1" x-text="t('common.delivery')"></span>
                        <span class="text-3xl font-normal text-indigo-700" x-text="t('common.days_count').replace(':count', '{{ $providerService->delivery_time_days }}')"></span>
                    </div>
                </div>

                <div>
                    <span class="block text-xs text-gray-400 uppercase font-normal tracking-widest mb-3" x-text="t('common.service_notes_terms')"></span>
                    <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100 text-gray-700 leading-relaxed italic">
                        {{ $providerService->provider_notes ?? __('common.no_specific_notes') }}
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Standardized Sidebar -->
        <div class="space-y-6">
            <h2 class="text-lg font-normal text-gray-900" x-text="t('common.standard_catalog')"></h2>
            <div class="rounded-3xl p-6 shadow-xl shadow-gray-200" style="background-color: {{ $settings->protection_block_bg_color ?? '#111827' }}">
                <p class="{{ $settings->protection_block_title_size ?? 'text-xs' }} {{ $settings->protection_block_title_weight ?? 'font-normal' }} uppercase tracking-widest mb-4"
                   style="color: {{ $settings->protection_block_title_color ?? '#9CA3AF' }}">{{ $settings->protection_block_title }}</p>
                <p class="{{ $settings->protection_block_description_size ?? 'text-sm' }} leading-relaxed mb-6"
                   style="color: {{ $settings->protection_block_description_color ?? '#D1D5DB' }}">{{ $settings->protection_block_description }}</p>
                <ul class="space-y-4">
                    @foreach($settings->protection_block_points as $point)
                    <li class="flex items-center gap-3 {{ $settings->protection_block_points_text_size ?? 'text-xs' }} font-normal"
                        style="color: {{ $settings->protection_block_points_text_color ?? 'white' }}">
                        <i data-lucide="{{ $point['icon'] }}" class="w-4 h-4" style="color: {{ $settings->protection_block_icon_color ?? 'var(--primary)' }}"></i>
                        <span>{{ $point['text'] }}</span>
                    </li>
                    @endforeach
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
                        <span class="px-4 py-1.5 bg-green-50 text-green-600 rounded-full text-[10px] font-normal uppercase tracking-widest border border-green-100" x-text="t('common.verified_expert')"></span>
                    </div>
                </div>

                <div class="mb-8">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-3xl font-normal text-gray-900" x-text="selectedProvider.name"></h2>
                        <div class="flex items-center gap-1.5 px-3 py-1 bg-yellow-50 border border-yellow-100 rounded-full">
                            <i data-lucide="star" class="w-4 h-4 text-yellow-400 fill-current"></i>
                            <span class="text-sm font-normal text-yellow-700" x-text="selectedProvider.averageRating"></span>
                        </div>
                    </div>
                    <p class="text-gray-500 text-sm font-normal leading-relaxed" x-text="selectedProvider.about || 'No company bio provided yet.'"></p>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-8">
                    <div class="bg-gray-50 p-6 rounded-3xl border border-gray-100 text-center">
                        <p class="text-[10px] font-normal text-gray-400 uppercase tracking-widest mb-1" x-text="t('common.total_clients')"></p>
                        <p class="text-2xl font-normal text-gray-900" x-text="selectedProvider.totalClients"></p>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-3xl border border-gray-100 text-center">
                        <p class="text-[10px] font-normal text-gray-400 uppercase tracking-widest mb-1" x-text="t('common.completed_projects')"></p>
                        <p class="text-2xl font-normal text-green-600" x-text="selectedProvider.completedProjects"></p>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-3xl border border-gray-100 text-center">
                        <p class="text-[10px] font-normal text-gray-400 uppercase tracking-widest mb-1" x-text="t('common.active_projects')"></p>
                        <p class="text-2xl font-normal text-primary" x-text="selectedProvider.activeProjects"></p>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-normal text-gray-900 flex items-center gap-2">
                        <i data-lucide="message-square-quote" class="w-5 h-5 text-primary"></i>
                        <span x-text="t('common.reviews')"></span>
                    </h3>
                    <div class="space-y-4">
                        <template x-if="selectedProvider.reviews && selectedProvider.reviews.length > 0">
                            <template x-for="review in selectedProvider.reviews" :key="review.project_name">
                                <div class="p-6 bg-white border border-gray-100 rounded-3xl shadow-sm">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gray-50 rounded-2xl flex items-center justify-center text-[10px] font-normal uppercase text-gray-400 overflow-hidden border border-gray-100">
                                                <template x-if="review.reviewer_logo">
                                                    <img :src="review.reviewer_logo" class="w-full h-full object-cover">
                                                </template>
                                                <template x-if="!review.reviewer_logo">
                                                    <span x-text="review.reviewer_name.substring(0, 2)"></span>
                                                </template>
                                            </div>
                                            <div>
                                                <p class="text-xs font-normal text-gray-900" x-text="review.reviewer_name"></p>
                                                <p class="text-[10px] text-gray-400 font-normal" x-text="review.project_name"></p>
                                            </div>
                                        </div>
                                        <div class="flex text-yellow-400">
                                            <template x-for="star in 5">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" :class="star <= review.rating ? 'fill-current text-yellow-400' : 'text-gray-200'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                            </template>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-600 leading-relaxed font-normal italic" x-text="'&quot;' + review.comment + '&quot;'"></p>
                                </div>
                            </template>
                        </template>
                        <template x-if="!selectedProvider.reviews || selectedProvider.reviews.length === 0">
                            <div class="p-8 text-center bg-gray-50 rounded-3xl border border-dashed border-gray-200">
                                <p class="text-sm text-gray-500 italic" x-text="t('common.no_reviews_yet')"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODALS -->
    @if(auth()->user()->isProviderMode())
    <!-- Add Service Modal -->
    <div x-show="addServiceModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center" style="display: none;">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="addServiceModalOpen = false"></div>
        <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl relative z-10 p-10 border border-gray-100 animate-in zoom-in duration-300">
            <div class="flex items-center justify-between mb-8"><h2 class="text-2xl font-normal" x-text="t('explore.add_service')"></h2><button @click="addServiceModalOpen = false" class="text-gray-400 hover:text-gray-600"><i data-lucide="x" class="w-6 h-6"></i></button></div>
            <form action="{{ route('provider.services.store') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="service_id" value="{{ $service->id }}">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1"><label class="text-[10px] font-normal uppercase tracking-widest text-gray-400" x-text="t('explore.price')"></label><input type="number" name="monthly_price" step="0.01" required class="w-full px-4 py-3 border border-gray-100 bg-gray-50 rounded-xl text-sm font-normal"></div>
                    <div class="space-y-1"><label class="text-[10px] font-normal uppercase tracking-widest text-gray-400">Annual Discount (%)</label><input type="number" name="annual_discount_percentage" min="0" max="100" value="0" class="w-full px-4 py-3 border border-gray-100 bg-gray-50 rounded-xl text-sm font-normal"></div>
                    <div class="space-y-1"><label class="text-[10px] font-normal uppercase tracking-widest text-gray-400" x-text="t('explore.days')"></label><input type="number" name="delivery_time_days" required class="w-full px-4 py-3 border border-gray-100 bg-gray-50 rounded-xl text-sm font-normal"></div>
                </div>
                <button type="submit" class="w-full py-4 bg-primary text-white rounded-xl font-normal" x-text="t('explore.add_to_portfolio_btn')"></button>
            </form>
        </div>
    </div>

    <!-- Edit Service Modal -->
    @if($providerService)
    <div x-show="editServiceModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center" style="display: none;">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="editServiceModalOpen = false"></div>
        <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl relative z-10 p-10 border border-gray-100 animate-in zoom-in duration-300">
            <div class="flex items-center justify-between mb-8"><h2 class="text-2xl font-normal" x-text="t('common.edit_service')"></h2><button @click="editServiceModalOpen = false" class="text-gray-400 hover:text-gray-600"><i data-lucide="x" class="w-6 h-6"></i></button></div>
            <form action="{{ route('provider.services.update', $providerService->id) }}" method="POST" class="space-y-6">
                @csrf @method('PATCH')
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1"><label class="text-[10px] font-normal uppercase tracking-widest text-gray-400" x-text="t('common.price')"></label><input type="number" name="monthly_price" step="0.01" value="{{ $providerService->monthly_price }}" required class="w-full px-4 py-3 border border-gray-100 bg-gray-50 rounded-xl text-sm font-normal"></div>
                    <div class="space-y-1"><label class="text-[10px] font-normal uppercase tracking-widest text-gray-400">Annual Discount (%)</label><input type="number" name="annual_discount_percentage" min="0" max="100" value="{{ $providerService->annual_discount_percentage }}" class="w-full px-4 py-3 border border-gray-100 bg-gray-50 rounded-xl text-sm font-normal"></div>
                    <div class="space-y-1"><label class="text-[10px] font-normal uppercase tracking-widest text-gray-400" x-text="t('common.days')"></label><input type="number" name="delivery_time_days" value="{{ $providerService->delivery_time_days }}" required class="w-full px-4 py-3 border border-gray-100 bg-gray-50 rounded-xl text-sm font-normal"></div>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-normal uppercase tracking-widest text-gray-400" x-text="t('common.additional_notes')"></label>
                    <textarea name="provider_notes" rows="4" class="w-full px-4 py-3 border border-gray-100 bg-gray-50 rounded-xl text-sm font-normal resize-none">{{ $providerService->provider_notes }}</textarea>
                </div>
                <button type="submit" class="w-full py-4 bg-gray-900 text-white rounded-xl font-normal" x-text="t('common.save')"></button>
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
