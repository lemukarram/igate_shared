@extends('layouts.app')

@section('content')
<div class="max-w-7xl w-full space-y-8 animate-in fade-in duration-700" x-data="{ providerInfoModalOpen: false, selectedProvider: {} }">
    <div>
        <h1 class="text-3xl font-normal text-gray-900" x-text="t('common.my_providers')"></h1>
        <p class="text-gray-500 mt-1" x-text="t('common.manage_your_active_providers_subtitle')"></p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($providers as $provider)
        <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
            <div class="flex items-center justify-between mb-8">
                <div class="w-16 h-16 bg-primary-light rounded-2xl flex items-center justify-center text-primary group-hover:scale-110 transition-transform overflow-hidden">
                    @if($provider->providerProfile && $provider->providerProfile->logo)
                        <img src="{{ asset('storage/' . $provider->providerProfile->logo) }}" class="w-full h-full object-cover">
                    @else
                        <i data-lucide="building-2" class="w-8 h-8"></i>
                    @endif
                </div>
                <div class="flex gap-2">
                    <button @click="selectedProvider = { 
                        id: '{{ $provider->id }}',
                        name: '{{ $provider->providerProfile->company_name ?? $provider->name }}',
                        logo: '{{ $provider->providerProfile && $provider->providerProfile->logo ? asset('storage/' . $provider->providerProfile->logo) : '' }}',
                        totalClients: '{{ $provider->total_clients_count }}',
                        completedProjects: '{{ $provider->completed_projects_count }}',
                        activeProjects: '{{ $provider->active_projects_count }}',
                        about: '{{ addslashes($provider->providerProfile->about ?? '') }}',
                        averageRating: '{{ number_format($provider->average_rating, 1) }}',
                        services: {{ $provider->providerServices->map(function($ps) {
                            return [
                                'id' => $ps->id,
                                'service_id' => $ps->service_id,
                                'name' => $ps->service->name,
                                'price' => number_format($ps->price, 0),
                                'delivery' => $ps->delivery_time_days
                            ];
                        })->toJson() }},
                        reviews: {{ $provider->reviewsReceived()->with(['reviewer', 'project.service'])->latest()->take(5)->get()->map(function($r) {
                            return [
                                'rating' => $r->rating,
                                'comment' => $r->comment,
                                'reviewer_name' => $r->reviewer->name,
                                'reviewer_logo' => $r->reviewer->profile_picture ? asset('storage/' . $r->reviewer->profile_picture) : null,
                                'project_name' => $r->project->service->name
                            ];
                        })->toJson() }}
                    }; providerInfoModalOpen = true" class="p-2 text-gray-400 hover:text-primary transition-colors">
                        <i data-lucide="eye" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
            <h3 class="text-xl font-normal text-gray-900 mb-2">{{ $provider->providerProfile->company_name ?? $provider->name }}</h3>
            <p class="text-sm text-gray-400 font-normal mb-6">{{ Str::limit($provider->providerProfile->about ?? __('common.no_bio_provided'), 100) }}</p>
            
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100 mb-6">
                <div class="text-center flex-1">
                    <span class="block text-[10px] font-normal text-gray-400 uppercase tracking-widest mb-1" x-text="t('common.rating')"></span>
                    <span class="text-lg font-normal text-gray-900 flex items-center justify-center gap-1">
                        <i data-lucide="star" class="w-4 h-4 text-yellow-400 fill-current"></i>
                        {{ number_format($provider->average_rating, 1) }}
                    </span>
                </div>
                <div class="w-px h-8 bg-gray-200"></div>
                <div class="text-center flex-1">
                    <span class="block text-[10px] font-normal text-gray-400 uppercase tracking-widest mb-1" x-text="t('common.projects')"></span>
                    <span class="text-lg font-normal text-gray-900">{{ $provider->providerProjects()->where('client_id', Auth::id())->count() }}</span>
                </div>
                <div class="w-px h-8 bg-gray-200"></div>
                <div class="text-center flex-1">
                    <span class="block text-[10px] font-normal text-gray-400 uppercase tracking-widest mb-1" x-text="t('common.chats')"></span>
                    <span class="text-lg font-normal text-gray-900">{{ $provider->pre_sale_chats_count }}</span>
                </div>
            </div>

            <button @click="selectedProvider = { 
                id: '{{ $provider->id }}',
                name: '{{ $provider->providerProfile->company_name ?? $provider->name }}',
                logo: '{{ $provider->providerProfile && $provider->providerProfile->logo ? asset('storage/' . $provider->providerProfile->logo) : '' }}',
                totalClients: '{{ $provider->total_clients_count }}',
                completedProjects: '{{ $provider->completed_projects_count }}',
                activeProjects: '{{ $provider->active_projects_count }}',
                about: '{{ addslashes($provider->providerProfile->about ?? '') }}',
                averageRating: '{{ number_format($provider->average_rating, 1) }}',
                services: {{ $provider->providerServices->map(function($ps) {
                    return [
                        'id' => $ps->id,
                        'service_id' => $ps->service_id,
                        'name' => $ps->service->name,
                        'price' => number_format($ps->price, 0),
                        'delivery' => $ps->delivery_time_days
                    ];
                })->toJson() }},
                reviews: {{ $provider->reviewsReceived()->with(['reviewer', 'project.service'])->latest()->take(5)->get()->map(function($r) {
                    return [
                        'rating' => $r->rating,
                        'comment' => $r->comment,
                        'reviewer_name' => $r->reviewer->name,
                        'reviewer_logo' => $r->reviewer->profile_picture ? asset('storage/' . $r->reviewer->profile_picture) : null,
                        'project_name' => $r->project->service->name
                    ];
                })->toJson() }}
            }; providerInfoModalOpen = true" class="w-full py-4 bg-gray-900 text-white rounded-2xl text-center text-sm font-normal hover:bg-black transition-all flex items-center justify-center gap-2">
                <span x-text="t('common.view_details')"></span>
                <i data-lucide="arrow-right" class="w-4 h-4 flip-rtl"></i>
            </button>
        </div>
        @empty
        <div class="col-span-full py-24 text-center bg-gray-50 rounded-[3rem] border-2 border-dashed border-gray-200">
            <div class="w-20 h-20 bg-white rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-sm">
                <i data-lucide="users" class="w-10 h-10 text-gray-300"></i>
            </div>
            <h3 class="text-2xl font-normal text-gray-900" x-text="t('common.no_providers_found')"></h3>
            <p class="text-gray-500 mt-2 max-w-sm mx-auto" x-text="t('common.no_providers_subtitle')"></p>
        </div>
        @endforelse
    </div>

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

                <!-- Services List -->
                <div class="space-y-4 mb-8">
                    <h3 class="text-lg font-normal text-gray-900 flex items-center gap-2">
                        <i data-lucide="layout-grid" class="w-5 h-5 text-primary"></i>
                        <span x-text="t('common.services_offered')"></span>
                    </h3>
                    <div class="grid grid-cols-1 gap-4">
                        <template x-for="service in selectedProvider.services" :key="service.id">
                            <div class="p-6 bg-white border border-gray-100 rounded-3xl shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
                                <div class="flex-1">
                                    <h4 class="font-normal text-gray-900" x-text="service.name"></h4>
                                    <div class="flex items-center gap-4 mt-1">
                                        <span class="text-xs text-gray-400 font-normal" x-text="t('common.price') + ': ' + service.price + ' ' + t('common.sar')"></span>
                                        <span class="text-xs text-gray-400 font-normal" x-text="t('common.delivery') + ': ' + service.delivery + ' ' + t('common.days')"></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 w-full md:w-auto">
                                    <a :href="'/explore/' + service.service_id + '/provider/' + selectedProvider.id + '/chat'" class="flex-1 md:flex-none p-3 bg-gray-50 text-gray-700 rounded-xl hover:bg-gray-100 transition-all border border-gray-100 flex items-center justify-center gap-2 font-normal text-sm">
                                        <i data-lucide="message-circle" class="w-5 h-5"></i>
                                        <span x-text="t('common.chat')"></span>
                                    </a>
                                    <a :href="'/checkout/' + service.id" class="flex-1 md:flex-none px-6 py-3 bg-primary text-white rounded-xl font-normal hover:bg-primary-dark transition-all shadow-sm text-center text-sm">
                                        <span x-text="t('common.request')"></span>
                                    </a>
                                </div>
                            </div>
                        </template>
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
</div>

<style>
    [dir="rtl"] .flip-rtl { transform: scaleX(-1); }
</style>
@endsection
