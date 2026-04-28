@extends('layouts.app')

@section('content')
<div class="w-full max-w-7xl mx-auto space-y-8 animate-in fade-in duration-700" x-data="{ releaseModal: false }">
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
            @if($projects->isNotEmpty())
            <a href="{{ route('projects.show', $projects->first()->id) }}" class="px-6 py-3 bg-primary text-white rounded-lg font-bold text-sm hover:bg-primary-dark transition-all flex items-center space-x-2 shadow-lg shadow-primary/20">
                <i data-lucide="message-square" class="w-4 h-4"></i>
                <span>Direct Chat</span>
            </a>
            @else
            <button disabled class="px-6 py-3 bg-gray-300 text-white rounded-lg font-bold text-sm flex items-center space-x-2 cursor-not-allowed">
                <i data-lucide="message-square" class="w-4 h-4"></i>
                <span>No Active Projects</span>
            </button>
            @endif
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
                    <a href="{{ route('projects.show', $p->id) }}" class="block p-6 rounded-lg bg-gray-50 border border-gray-100 group hover:border-primary transition-all">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-10 h-10 bg-white rounded-md flex items-center justify-center text-primary shadow-sm">
                                <i data-lucide="{{ $p->service->icon ?? 'briefcase' }}" class="w-5 h-5"></i>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-primary">{{ $p->status }}</span>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-1">{{ $p->service->name }}</h4>
                        <p class="text-xs text-gray-400 mb-4 italic">Total: {{ number_format($p->total_amount, 0) }} SAR</p>
                        <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest">
                            <span class="text-gray-400">Progress</span>
                            <span class="text-primary">65%</span>
                        </div>
                        <div class="h-1.5 w-full bg-white rounded-full overflow-hidden mt-2 border border-gray-100">
                            <div class="h-full bg-primary w-[65%] rounded-full"></div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-lg p-8 shadow-sm">
                <h3 class="text-xl font-bold mb-6">Recent Activity</h3>
                <div class="space-y-6">
                    @foreach(['Payment for HR Management released from escrow', 'New document uploaded to Vault', 'Milestone approved by client'] as $activity)
                    <div class="flex items-start space-x-4">
                        <div class="w-2 h-2 rounded-full bg-primary mt-2"></div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">{{ $activity }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Recently</p>
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
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Company Info</p>
                        <p class="text-sm font-bold text-gray-900">{{ $client->companies->first()->name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500 font-medium italic">{{ $client->companies->first()->industry ?? '' }}</p>
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
                    <h3 class="text-3xl font-black mb-4">{{ number_format($projects->sum('total_amount'), 0) }} <span class="text-xs">SAR</span></h3>
                    <button @click="releaseModal = true" class="w-full py-3 bg-white/20 hover:bg-white/30 rounded-md font-bold text-xs transition-all backdrop-blur-md border border-white/10 uppercase tracking-widest">Release Schedule</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Release Schedule Modal -->
    <div x-show="releaseModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
        <div @click.away="releaseModal = false" class="bg-white rounded-lg shadow-xl w-full max-w-2xl border border-gray-100 overflow-hidden animate-in zoom-in duration-200">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Release Schedule</h2>
                    <p class="text-xs text-gray-500 mt-1">Payments held in Escrow for {{ $client->name }}</p>
                </div>
                <button @click="releaseModal = false" class="text-gray-400 hover:text-gray-600"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[60vh]">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-[10px] uppercase tracking-widest text-gray-400">
                        <tr>
                            <th class="px-4 py-3 font-bold">Project</th>
                            <th class="px-4 py-3 font-bold">Amount</th>
                            <th class="px-4 py-3 font-bold">Status</th>
                            <th class="px-4 py-3 font-bold text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($projects as $p)
                        <tr class="hover:bg-gray-50 transition-all">
                            <td class="px-4 py-4 font-bold">{{ $p->service->name }}</td>
                            <td class="px-4 py-4 font-bold text-gray-900">{{ number_format($p->total_amount, 0) }} SAR</td>
                            <td class="px-4 py-4">
                                <span class="px-2 py-1 bg-yellow-50 text-yellow-600 rounded text-[10px] font-bold uppercase tracking-wider">In Escrow</span>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <button class="px-3 py-1.5 bg-white border border-gray-200 text-gray-600 hover:text-primary hover:border-primary rounded text-xs font-bold transition-all">Request Release</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
