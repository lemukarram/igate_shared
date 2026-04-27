@extends('layouts.app')

@section('content')
<div class="w-full max-w-7xl mx-auto space-y-10 animate-in fade-in duration-700">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight">My Clients</h1>
            <p class="text-gray-500 font-medium mt-1 text-lg">Manage relationships and active subscriptions.</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-primary-light p-8 rounded-lg border border-primary/10">
            <p class="text-[10px] font-black uppercase tracking-widest text-primary mb-2">Total Clients</p>
            <h3 class="text-3xl font-black text-primary-dark">{{ $clients->count() }}</h3>
        </div>
        <div class="bg-primary-light p-8 rounded-lg border border-primary/10">
            <p class="text-[10px] font-black uppercase tracking-widest text-primary mb-2">Active Subscriptions</p>
            <h3 class="text-3xl font-black text-primary-dark">{{ $ongoingProjects->count() }}</h3>
        </div>
        <div class="bg-primary-light p-8 rounded-lg border border-primary/10">
            <p class="text-[10px] font-black uppercase tracking-widest text-primary mb-2">Retention Rate</p>
            <h3 class="text-3xl font-black text-primary-dark">85%</h3>
        </div>
    </div>

    <!-- Client List -->
    <div class="bg-white border border-gray-100 rounded-lg overflow-hidden shadow-sm">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Client Name</th>
                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Subscribed Services</th>
                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Status</th>
                    <th class="px-8 py-5 text-right text-[10px] font-black uppercase tracking-widest text-gray-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($clients as $clientId => $projects)
                <tr class="hover:bg-gray-50 transition-all group">
                    <td class="px-8 py-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-primary-light rounded-lg flex items-center justify-center text-primary font-black">
                                {{ substr($projects->first()->client->name, 0, 2) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $projects->first()->client->name }}</p>
                                <p class="text-xs text-gray-400">{{ $projects->first()->client->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex flex-wrap gap-2">
                            @foreach($projects as $p)
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-md text-[10px] font-bold">{{ $p->service->name }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[10px] font-black uppercase tracking-widest">Active</span>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('provider.clients.show', $clientId) }}" class="p-2 hover:bg-white hover:shadow-sm rounded-md transition-all text-gray-400 hover:text-primary">
                                <i data-lucide="eye" class="w-5 h-5"></i>
                            </a>
                            <button class="p-2 hover:bg-white hover:shadow-sm rounded-md transition-all text-gray-400 hover:text-primary">
                                <i data-lucide="message-square" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
