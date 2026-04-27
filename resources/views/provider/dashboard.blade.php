@extends('layouts.app')

@section('content')
<div class="w-full max-w-7xl mx-auto space-y-10 animate-in fade-in slide-in-from-bottom-6 duration-700">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight">Provider Dashboard</h1>
            <p class="text-gray-500 font-medium mt-1 text-lg">Real-time overview of your agency's performance.</p>
        </div>
        <div class="flex items-center space-x-3">
            <span class="px-4 py-2 bg-green-50 text-green-600 rounded-xl text-sm font-black uppercase tracking-widest border border-green-100">Live Status: Active</span>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-8 rounded-[2.5rem] shadow-xl shadow-blue-100 text-white relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-2">Total Revenue</p>
                <h3 class="text-3xl font-black mb-1 leading-none">124,500 <span class="text-sm">SAR</span></h3>
                <p class="text-xs font-bold text-blue-100">+12.5% this month</p>
            </div>
            <i data-lucide="banknote" class="absolute -right-4 -bottom-4 w-24 h-24 text-white/10 group-hover:scale-110 transition-transform"></i>
        </div>

        <div class="bg-gradient-to-br from-emerald-500 to-teal-700 p-8 rounded-[2.5rem] shadow-xl shadow-emerald-100 text-white relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-2">Active Clients</p>
                <h3 class="text-3xl font-black mb-1 leading-none">42</h3>
                <p class="text-xs font-bold text-emerald-100">8 new this week</p>
            </div>
            <i data-lucide="users-2" class="absolute -right-4 -bottom-4 w-24 h-24 text-white/10 group-hover:scale-110 transition-transform"></i>
        </div>

        <div class="bg-gradient-to-br from-rose-500 to-pink-700 p-8 rounded-[2.5rem] shadow-xl shadow-rose-100 text-white relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-2">SLA Compliance</p>
                <h3 class="text-3xl font-black mb-1 leading-none">99.2%</h3>
                <p class="text-xs font-bold text-rose-100">Target: 98.0%</p>
            </div>
            <i data-lucide="shield-check" class="absolute -right-4 -bottom-4 w-24 h-24 text-white/10 group-hover:scale-110 transition-transform"></i>
        </div>

        <div class="bg-gradient-to-br from-amber-500 to-orange-600 p-8 rounded-[2.5rem] shadow-xl shadow-amber-100 text-white relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-2">Pending Tasks</p>
                <h3 class="text-3xl font-black mb-1 leading-none">18</h3>
                <p class="text-xs font-bold text-amber-100">5 due today</p>
            </div>
            <i data-lucide="clock" class="absolute -right-4 -bottom-4 w-24 h-24 text-white/10 group-hover:scale-110 transition-transform"></i>
        </div>
    </div>

    <!-- Charts Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-white border border-gray-100 rounded-[2.5rem] p-8 shadow-sm">
            <div class="flex items-center justify-between mb-10">
                <h3 class="text-xl font-bold">Revenue Growth</h3>
                <select class="bg-gray-50 border-none rounded-xl text-xs font-bold py-2 px-4 focus:ring-0">
                    <option>Last 6 Months</option>
                    <option>Last 12 Months</option>
                </select>
            </div>
            <div class="h-80 w-full flex items-center justify-center bg-gray-50 rounded-[2rem] border-2 border-dashed border-gray-100">
                <p class="text-gray-400 font-bold text-sm uppercase tracking-widest italic">Chart.js Placeholder: Revenue Data</p>
            </div>
        </div>

        <div class="bg-white border border-gray-100 rounded-[2.5rem] p-8 shadow-sm">
            <h3 class="text-xl font-bold mb-10">Client Retention</h3>
            <div class="space-y-8">
                <div class="space-y-2">
                    <div class="flex justify-between text-sm font-bold">
                        <span>New Clients</span>
                        <span class="text-blue-600">65%</span>
                    </div>
                    <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-600 w-[65%] rounded-full"></div>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm font-bold">
                        <span>Returning</span>
                        <span class="text-indigo-600">35%</span>
                    </div>
                    <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-indigo-600 w-[35%] rounded-full"></div>
                    </div>
                </div>
                <div class="pt-8 border-t border-gray-50">
                    <div class="p-6 bg-blue-50 rounded-3xl">
                        <p class="text-xs font-black text-blue-900 uppercase tracking-widest mb-2 italic">Pro Tip</p>
                        <p class="text-xs text-blue-700 leading-relaxed font-medium">Standardized services lead to 40% higher client retention rates on iGate.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
