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
                <h3 class="text-3xl font-black mb-1 leading-none">124,500 <span class="text-sm" x-text="lang === 'ar' ? 'ر.س' : 'SAR'"></span></h3>
                <p class="text-xs font-bold text-blue-100" x-text="t('common.month_growth').replace(':percent', '+12.5%')"></p>
            </div>
            <i data-lucide="banknote" class="absolute -end-4 -bottom-4 w-24 h-24 text-white/10 group-hover:scale-110 transition-transform flip-rtl"></i>
        </div>

        <div class="bg-gradient-to-br from-emerald-500 to-teal-700 p-8 rounded-[2.5rem] shadow-xl shadow-emerald-100 text-white relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-2" x-text="t('common.active_clients')"></p>
                <h3 class="text-3xl font-black mb-1 leading-none">42</h3>
                <p class="text-xs font-bold text-emerald-100" x-text="t('common.new_this_week').replace(':count', '8')"></p>
            </div>
            <i data-lucide="users-2" class="absolute -end-4 -bottom-4 w-24 h-24 text-white/10 group-hover:scale-110 transition-transform flip-rtl"></i>
        </div>

        <div class="bg-gradient-to-br from-rose-500 to-pink-700 p-8 rounded-[2.5rem] shadow-xl shadow-rose-100 text-white relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-2" x-text="t('common.sla_compliance')"></p>
                <h3 class="text-3xl font-black mb-1 leading-none">99.2%</h3>
                <p class="text-xs font-bold text-rose-100" x-text="t('common.target').replace(':target', '98.0%')"></p>
            </div>
            <i data-lucide="shield-check" class="absolute -end-4 -bottom-4 w-24 h-24 text-white/10 group-hover:scale-110 transition-transform flip-rtl"></i>
        </div>

        <div class="bg-gradient-to-br from-amber-500 to-orange-600 p-8 rounded-[2.5rem] shadow-xl shadow-amber-100 text-white relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-2" x-text="t('common.pending_tasks')"></p>
                <h3 class="text-3xl font-black mb-1 leading-none">18</h3>
                <p class="text-xs font-bold text-amber-100" x-text="t('common.due_today').replace(':count', '5')"></p>
            </div>
            <i data-lucide="clock" class="absolute -end-4 -bottom-4 w-24 h-24 text-white/10 group-hover:scale-110 transition-transform flip-rtl"></i>
        </div>
    </div>

    <!-- Charts Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-white border border-gray-100 rounded-[2.5rem] p-8 shadow-sm">
            <div class="flex items-center justify-between mb-10">
                <h3 class="text-xl font-bold" x-text="t('common.revenue_growth')"></h3>
                <select class="bg-gray-50 border-none rounded-xl text-xs font-bold py-2 px-4 focus:ring-0">
                    <option x-text="t('common.last_6_months')"></option>
                    <option x-text="t('common.last_12_months')"></option>
                </select>
            </div>
            <div class="h-80 w-full flex items-center justify-center bg-gray-50 rounded-[2rem] border-2 border-dashed border-gray-100">
                <p class="text-gray-400 font-bold text-sm uppercase tracking-widest italic" x-text="lang === 'ar' ? 'مكان الرسم البياني: بيانات الإيرادات' : 'Chart.js Placeholder: Revenue Data'"></p>
            </div>
        </div>

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
                <div class="pt-8 border-t border-gray-50">
                    <div class="p-6 bg-blue-50 rounded-3xl">
                        <p class="text-xs font-black text-blue-900 uppercase tracking-widest mb-2 italic" x-text="t('common.pro_tip')"></p>
                        <p class="text-xs text-blue-700 leading-relaxed font-medium" x-text="t('common.standardized_retention_tip')"></p>
                    </div>
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
