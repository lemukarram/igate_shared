@extends('layouts.app')

@section('content')
<div class="max-w-7xl w-full space-y-8 animate-in fade-in duration-700">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap:4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900" x-text="t('common.my_services')"></h1>
            <p class="text-gray-500 mt-1" x-text="t('common.manage_pricing_scope')"></p>
        </div>
        <button @click="addServiceOpen = true" class="px-6 py-3 bg-primary text-white rounded-xl font-bold hover:bg-primary-dark transition-all flex items-center gap-2 shadow-lg shadow-primary/20">
            <i data-lucide="plus" class="w-5 h-5"></i>
            <span x-text="t('explore.add_new_service')"></span>
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- My Active Offerings -->
        <div class="lg:col-span-2 space-y-6">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-500"></i>
                <span x-text="t('common.active_offerings')"></span>
            </h2>
            
            <div class="grid grid-cols-2 gap-4">
                @forelse($myServices as $ps)
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-primary-light rounded-xl flex items-center justify-center text-primary">
                                <i data-lucide="{{ $ps->service->icon }}" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg">{{ $ps->service->name }}</h3>
                                <span class="text-[10px] font-black uppercase text-gray-400 tracking-widest">{{ $ps->service->category }}</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('explore.show', $ps->service->id) }}" class="p-2 text-gray-400 hover:text-primary transition-colors">
                                <i data-lucide="edit-3" class="w-5 h-5"></i>
                            </a>
                            <form action="{{ route('provider.services.destroy', $ps->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-500 transition-colors">
                                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <div>
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1" x-text="t('common.status')"></span>
                            <span class="text-xs font-bold text-emerald-600 uppercase" x-text="t('common.active')"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1" x-text="t('common.delivery')"></span>
                            <span class="text-sm font-bold text-gray-900" x-text="t('common.days_count').replace(':count', '{{ $ps->delivery_time_days }}')"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1" x-text="t('common.clients')"></span>
                            <span class="text-sm font-bold text-gray-900">0</span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1" x-text="t('common.rating')"></span>
                            <span class="flex items-center text-sm font-bold text-amber-500">
                                <i data-lucide="star" class="w-3 h-3 fill-current mr-1"></i> 5.0
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                    <p class="text-gray-500 font-medium" x-text="t('common.no_services_yet')"></p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Catalog Sidebar -->
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
</div>
@endsection
