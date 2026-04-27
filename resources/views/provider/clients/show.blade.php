@extends('layouts.app')

@section('content')
<div class="w-full max-w-7xl mx-auto space-y-8 animate-in fade-in duration-700">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-6">
            <a href="{{ route('provider.clients') }}" class="w-10 h-10 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-900 transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-primary-light rounded-lg flex items-center justify-center text-primary font-black text-2xl shadow-sm border border-primary/10">
                    {{ substr($client->name, 0, 2) }}
                </div>
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">{{ $client->name }}</h1>
                    <p class="text-gray-500 font-medium">Enterprise Client • Member since {{ $client->created_at->format('M Y') }}</p>
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button class="px-6 py-3 bg-primary text-white rounded-lg font-bold text-sm hover:bg-primary-dark transition-all flex items-center space-x-2 shadow-lg shadow-primary/20">
                <i data-lucide="message-square" class="w-4 h-4"></i>
                <span>Direct Chat</span>
            </button>
            <button class="p-3 bg-gray-50 text-gray-400 rounded-lg hover:bg-gray-100 transition-all border border-gray-100">
                <i data-lucide="more-vertical" class="w-5 h-5"></i>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white border border-gray-100 rounded-lg p-8 shadow-sm">
                <h3 class="text-xl font-bold mb-6">Subscription Overview</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($projects as $p)
                    <div class="p-6 rounded-lg bg-gray-50 border border-gray-100 group hover:border-primary transition-all">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-10 h-10 bg-white rounded-md flex items-center justify-center text-primary shadow-sm">
                                <i data-lucide="{{ $p->service->icon }}" class="w-5 h-5"></i>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-primary">Active</span>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-1">{{ $p->service->name }}</h4>
                        <p class="text-xs text-gray-400 mb-4 italic">Billed monthly: {{ number_format($p->total_amount, 0) }} SAR</p>
                        <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest">
                            <span class="text-gray-400">Progress</span>
                            <span class="text-primary">65%</span>
                        </div>
                        <div class="h-1.5 w-full bg-white rounded-full overflow-hidden mt-2 border border-gray-100">
                            <div class="h-full bg-primary w-[65%] rounded-full"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-lg p-8 shadow-sm">
                <h3 class="text-xl font-bold mb-6">Recent Activity</h3>
                <div class="space-y-6">
                    @foreach(['Payment for HR Management released from escrow', 'New document uploaded to Vault: Commercial Register.pdf', 'Milestone "Phase 1 Complete" approved by client'] as $activity)
                    <div class="flex items-start space-x-4">
                        <div class="w-2 h-2 rounded-full bg-primary mt-2"></div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">{{ $activity }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">2 hours ago</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar Details -->
        <div class="space-y-8">
            <div class="bg-gray-50 border border-gray-100 rounded-lg p-8">
                <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-6">Client Details</h3>
                <div class="space-y-6">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Tax Number</p>
                        <p class="text-sm font-bold text-gray-900">310029384700003</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Company Size</p>
                        <p class="text-sm font-bold text-gray-900">50 - 200 Employees</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Primary Contact</p>
                        <p class="text-sm font-bold text-gray-900">{{ $client->name }}</p>
                        <p class="text-xs text-gray-500 font-medium italic">{{ $client->email }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-primary text-white rounded-lg p-8 shadow-xl shadow-primary/20 relative overflow-hidden group">
                <i data-lucide="shield-check" class="absolute -right-4 -bottom-4 w-24 h-24 text-white/10 group-hover:scale-110 transition-transform"></i>
                <div class="relative z-10">
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-80 mb-2">Escrow Balance</p>
                    <h3 class="text-3xl font-black mb-4">12,000 <span class="text-xs">SAR</span></h3>
                    <button class="w-full py-3 bg-white/20 hover:bg-white/30 rounded-md font-bold text-xs transition-all backdrop-blur-md border border-white/10 uppercase tracking-widest">Release Schedule</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
