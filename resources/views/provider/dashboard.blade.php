@extends('layouts.app')

@section('content')
<div class="w-full max-w-7xl mx-auto space-y-10 animate-in fade-in slide-in-from-bottom-6 duration-700" 
     x-data="providerDashboard({ initialStatus: '{{ Auth::user()->providerProfile->status ?? 'inactive' }}' })">
    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight" x-text="t('common.dashboard')"></h1>
            <p class="text-gray-500 font-medium mt-1 text-lg" x-text="t('common.agency_performance')"></p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="statusModalOpen = true" 
                  :class="status === 'active' ? 'bg-green-50 text-green-600 border-green-100 hover:bg-green-100' : 'bg-gray-50 text-gray-400 border-gray-100 hover:bg-gray-100'"
                  class="px-4 py-2 rounded-[0.5rem] text-sm font-black uppercase tracking-widest border transition-all flex items-center gap-2">
                <div class="w-2 h-2 rounded-full" :class="status === 'active' ? 'bg-green-500' : 'bg-gray-300'"></div>
                <span x-text="t('common.live_status') + ': ' + (status === 'active' ? t('common.active') : t('common.inactive'))"></span>
            </button>
        </div>

        <!-- Status Toggle Modal -->
        <div x-show="statusModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center" style="display: none;">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="statusModalOpen = false"></div>
            <div class="bg-white w-full max-w-sm rounded-xl shadow-2xl relative z-10 p-6 border border-gray-100">
                <h2 class="text-xl font-bold mb-4" x-text="t('common.change_live_status')"></h2>
                <div class="space-y-2">
                    <button @click="updateStatus('active')" class="w-full flex items-center gap-3 p-3 border border-gray-100 rounded-lg cursor-pointer hover:bg-gray-50 text-green-600 font-bold" :class="status === 'active' ? 'bg-green-50 border-green-200' : ''">
                        <div class="w-2 h-2 rounded-full bg-green-500"></div>
                        <span x-text="t('common.active')"></span>
                    </button>
                    <button @click="updateStatus('inactive')" class="w-full flex items-center gap-3 p-3 border border-gray-100 rounded-lg cursor-pointer hover:bg-gray-50 text-gray-400 font-bold" :class="status === 'inactive' ? 'bg-gray-50 border-gray-200' : ''">
                        <div class="w-2 h-2 rounded-full bg-gray-300"></div>
                        <span x-text="t('common.inactive')"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-8 rounded-[2.5rem] shadow-xl shadow-blue-100 text-white relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-2" x-text="t('common.total_revenue')"></p>
                <h3 class="text-3xl font-black mb-1 leading-none">{{ number_format($totalRevenue) }} <span class="text-sm" x-text="lang === 'ar' ? 'ر.س' : 'SAR'"></span></h3>
                <p class="text-xs font-bold text-blue-100">{{ $growth >= 0 ? '+' : '' }}{{ number_format($growth, 1) }}% this month</p>
            </div>
            <i data-lucide="banknote" class="absolute -end-4 -bottom-4 w-24 h-24 text-white/10 group-hover:scale-110 transition-transform flip-rtl"></i>
        </div>

        <div class="bg-gradient-to-br from-emerald-500 to-teal-700 p-8 rounded-[2.5rem] shadow-xl shadow-emerald-100 text-white relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-2" x-text="t('common.active_clients')"></p>
                <h3 class="text-3xl font-black mb-1 leading-none">{{ $activeClientsCount }}</h3>
                <p class="text-xs font-bold text-emerald-100">Across all active projects</p>
            </div>
            <i data-lucide="users-2" class="absolute -end-4 -bottom-4 w-24 h-24 text-white/10 group-hover:scale-110 transition-transform flip-rtl"></i>
        </div>

        <div class="bg-gradient-to-br from-rose-500 to-pink-700 p-8 rounded-[2.5rem] shadow-xl shadow-rose-100 text-white relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-2" x-text="t('common.sla_compliance')"></p>
                <h3 class="text-3xl font-black mb-1 leading-none">{{ number_format($slaCompliant, 1) }}%</h3>
                <p class="text-xs font-bold text-rose-100" x-text="t('common.target').replace(':target', '98.0%')"></p>
            </div>
            <i data-lucide="shield-check" class="absolute -end-4 -bottom-4 w-24 h-24 text-white/10 group-hover:scale-110 transition-transform flip-rtl"></i>
        </div>

        <div class="bg-gradient-to-br from-amber-500 to-orange-600 p-8 rounded-[2.5rem] shadow-xl shadow-amber-100 text-white relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-2" x-text="t('common.pending_tasks')"></p>
                <h3 class="text-3xl font-black mb-1 leading-none">{{ $pendingTasksCount }}</h3>
                <p class="text-xs font-bold text-amber-100">Tasks in To-Do or In-Progress</p>
            </div>
            <i data-lucide="clock" class="absolute -end-4 -bottom-4 w-24 h-24 text-white/10 group-hover:scale-110 transition-transform flip-rtl"></i>
        </div>
    </div>

    <!-- Charts Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white border border-gray-100 rounded-[2.5rem] p-8 shadow-sm">
                <div class="flex items-center justify-between mb-10">
                    <h3 class="text-xl font-bold" x-text="t('common.revenue_growth')"></h3>
                    <select class="bg-gray-50 border-none rounded-xl text-xs font-bold py-2 px-4 focus:ring-0">
                        <option x-text="t('common.last_6_months')"></option>
                        <option x-text="t('common.last_12_months')"></option>
                    </select>
                </div>
                <div class="h-80 w-full flex items-center justify-center bg-gray-50 rounded-[2rem] border-2 border-dashed border-gray-100">
                    <p class="text-gray-400 font-bold text-sm uppercase tracking-widest italic" x-text="t('common.revenue_data_placeholder')"></p>
                </div>
            </div>

            <!-- Recent Projects -->
            <div class="bg-white border border-gray-100 rounded-[2.5rem] p-8 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-bold" x-text="t('common.ongoing_projects')"></h3>
                    <a href="{{ route('provider.clients') }}" class="text-xs font-black text-primary uppercase tracking-widest hover:underline" x-text="t('common.view_all')"></a>
                </div>
                <div class="space-y-4">
                    @forelse($recentProjects as $p)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-primary shadow-sm">
                                <i data-lucide="{{ $p->service->icon }}" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">{{ $p->service->name }}</h4>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $p->client->name }}</p>
                            </div>
                        </div>
                        <a href="{{ route('projects.show', $p->id) }}" class="p-2 bg-white text-gray-400 hover:text-primary rounded-lg transition-colors">
                            <i data-lucide="external-link" class="w-4 h-4"></i>
                        </a>
                    </div>
                    @empty
                    <p class="text-center text-gray-400 text-sm italic py-4" x-text="t('common.no_active_projects')"></p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <div class="bg-white border border-gray-100 rounded-[2.5rem] p-8 shadow-sm">
                <h3 class="text-xl font-bold mb-10" x-text="t('common.client_retention')"></h3>
                <div class="space-y-8">
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm font-bold">
                            <span x-text="t('common.new_clients')"></span>
                            <span class="text-blue-600">65%</span>
                        </div>
                        <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-600 w-[65%] rounded-full"></div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm font-bold">
                            <span x-text="t('common.returning')"></span>
                            <span class="text-indigo-600">35%</span>
                        </div>
                        <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-indigo-600 w-[35%] rounded-full"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Pre-sale Chats -->
            <div class="bg-white border border-gray-100 rounded-[2.5rem] p-8 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-lg font-bold" x-text="t('common.pre_sale_chats')"></h3>
                </div>
                <div class="space-y-4">
                    @forelse($recentChats as $chat)
                    <a href="{{ route('explore.chat', ['serviceId' => $chat->service_id, 'providerId' => $chat->provider_id, 'client_id' => $chat->client_id]) }}" class="block p-4 bg-gray-50 rounded-2xl border border-gray-100 hover:border-primary/20 hover:bg-primary-light transition-all group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-primary shadow-sm group-hover:scale-110 transition-transform">
                                <i data-lucide="{{ $chat->service->icon }}" class="w-4 h-4"></i>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-gray-900 truncate">{{ $chat->service->name }}</h4>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest truncate">{{ $chat->client->name }}</p>
                            </div>
                        </div>
                    </a>
                    @empty
                    <p class="text-center text-gray-400 text-[10px] font-black uppercase tracking-widest italic py-4" x-text="t('common.no_pre_sale_chats_yet')"></p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function providerDashboard(config) {
        return {
            status: config.initialStatus,
            statusModalOpen: false,
            async updateStatus(newStatus) {
                try {
                    const response = await fetch('{{ route('settings.status') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ status: newStatus })
                    });
                    const data = await response.json();
                    if (data.success) {
                        this.status = data.status;
                        this.statusModalOpen = false;
                    }
                } catch (error) {
                    console.error('Error updating status:', error);
                }
            }
        }
    }
</script>
@endsection
