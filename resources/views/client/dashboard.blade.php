@extends('layouts.app')

@section('content')
<div class="w-full max-w-7xl mx-auto space-y-10 animate-in fade-in slide-in-from-bottom-6 duration-700">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight">Welcome, {{ Auth::user()->name }}</h1>
            <p class="text-gray-500 font-medium mt-1 text-lg">Manage your business operations and active subscriptions.</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-indigo-600 to-blue-700 p-8 rounded-[2.5rem] shadow-xl shadow-blue-100 text-white relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-2">Active Projects</p>
                <h3 class="text-3xl font-black mb-1 leading-none">{{ $ongoingProjects->count() }}</h3>
                <p class="text-xs font-bold text-blue-100">Across 3 categories</p>
            </div>
            <i data-lucide="layers" class="absolute -right-4 -bottom-4 w-24 h-24 text-white/10 group-hover:scale-110 transition-transform"></i>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-indigo-700 p-8 rounded-[2.5rem] shadow-xl shadow-purple-100 text-white relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-2">Total Invested</p>
                <h3 class="text-3xl font-black mb-1 leading-none">48,200 <span class="text-sm">SAR</span></h3>
                <p class="text-xs font-bold text-purple-100">Held in escrow: 12,000 SAR</p>
            </div>
            <i data-lucide="shield-check" class="absolute -right-4 -bottom-4 w-24 h-24 text-white/10 group-hover:scale-110 transition-transform"></i>
        </div>

        <div class="bg-gradient-to-br from-emerald-500 to-teal-700 p-8 rounded-[2.5rem] shadow-xl shadow-emerald-100 text-white relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-2">Completed Tasks</p>
                <h3 class="text-3xl font-black mb-1 leading-none">124</h3>
                <p class="text-xs font-bold text-emerald-100">85% efficiency rate</p>
            </div>
            <i data-lucide="check-circle-2" class="absolute -right-4 -bottom-4 w-24 h-24 text-white/10 group-hover:scale-110 transition-transform"></i>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-rose-600 p-8 rounded-[2.5rem] shadow-xl shadow-amber-100 text-white relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-2">Messages</p>
                <h3 class="text-3xl font-black mb-1 leading-none">12</h3>
                <p class="text-xs font-bold text-amber-100">3 unread notifications</p>
            </div>
            <i data-lucide="message-square" class="absolute -right-4 -bottom-4 w-24 h-24 text-white/10 group-hover:scale-110 transition-transform"></i>
        </div>
    </div>

    <!-- Main Workspace Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Projects -->
        <div class="bg-white border border-gray-100 rounded-[2.5rem] p-8 shadow-sm">
            <h3 class="text-xl font-bold mb-8">Recent Projects</h3>
            <div class="space-y-4">
                @forelse($ongoingProjects->take(3) as $p)
                <a href="{{ route('projects.show', $p->id) }}" class="flex items-center justify-between p-5 rounded-3xl bg-gray-50 hover:bg-blue-50 hover:scale-[1.02] transition-all border border-transparent hover:border-blue-100">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-blue-600 shadow-sm">
                            <i data-lucide="{{ $p->service->icon }}" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $p->service->name }}</p>
                            <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest">{{ $p->provider->providerProfile->company_name }}</p>
                        </div>
                    </div>
                    <i data-lucide="chevron-right" class="w-5 h-5 text-gray-300"></i>
                </a>
                @empty
                <div class="py-12 text-center">
                    <p class="text-gray-400 text-sm font-medium italic">No active projects yet.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white border border-gray-100 rounded-[2.5rem] p-8 shadow-sm">
            <h3 class="text-xl font-bold mb-8">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('explore.index') }}" class="p-6 bg-blue-50 rounded-[2rem] border border-blue-100 hover:bg-blue-600 hover:text-white transition-all group">
                    <i data-lucide="plus-circle" class="w-8 h-8 mb-4 group-hover:text-white text-blue-600 transition-colors"></i>
                    <p class="font-bold">New Request</p>
                    <p class="text-[10px] opacity-70">Browse the catalog</p>
                </a>
                <div class="p-6 bg-purple-50 rounded-[2rem] border border-purple-100 hover:bg-purple-600 hover:text-white transition-all group cursor-pointer">
                    <i data-lucide="credit-card" class="w-8 h-8 mb-4 group-hover:text-white text-purple-600 transition-colors"></i>
                    <p class="font-bold">Billing</p>
                    <p class="text-[10px] opacity-70">Manage payments</p>
                </div>
                <div class="p-6 bg-emerald-50 rounded-[2rem] border border-emerald-100 hover:bg-emerald-600 hover:text-white transition-all group cursor-pointer">
                    <i data-lucide="life-buoy" class="w-8 h-8 mb-4 group-hover:text-white text-emerald-600 transition-colors"></i>
                    <p class="font-bold">Support</p>
                    <p class="text-[10px] opacity-70">Get help fast</p>
                </div>
                <div class="p-6 bg-gray-50 rounded-[2rem] border border-gray-100 hover:bg-gray-900 hover:text-white transition-all group cursor-pointer">
                    <i data-lucide="settings" class="w-8 h-8 mb-4 group-hover:text-white text-gray-900 transition-colors"></i>
                    <p class="font-bold">Settings</p>
                    <p class="text-[10px] opacity-70">System config</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
