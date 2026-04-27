@extends('layouts.app')

@section('content')
<div class="w-full max-w-6xl mx-auto h-full flex flex-col space-y-6 animate-in fade-in duration-700">
    <!-- Top Progress Bar -->
    <div class="bg-white border border-gray-100 rounded-lg p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-primary-light rounded-lg flex items-center justify-center text-primary">
                    <i data-lucide="{{ $project->service->icon }}" class="w-6 h-6"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-gray-900 tracking-tight">{{ $project->service->name }}</h1>
                    <p class="text-xs font-black uppercase tracking-widest text-gray-400">Project #{{ str_pad($project->id, 5, '0', STR_PAD_LEFT) }} • Active</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-sm font-black text-primary uppercase tracking-widest">65% Completed</span>
            </div>
        </div>
        <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full bg-primary w-[65%] rounded-full shadow-sm"></div>
        </div>
    </div>

    <!-- Main Content Area (2 Columns) -->
    <div class="flex-1 grid grid-cols-1 lg:grid-cols-3 gap-6 min-h-0">
        <!-- Left: Project & Client Details + Sub-tasks -->
        <div class="lg:col-span-2 space-y-6 overflow-y-auto custom-scrollbar pr-2">
            <!-- Details -->
            <div class="bg-white border border-gray-100 rounded-lg p-8 shadow-sm">
                <div class="grid grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4">Client Overview</h3>
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center text-white font-bold text-xs uppercase">
                                {{ substr($project->client->name, 0, 2) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $project->client->name }}</p>
                                <p class="text-xs text-gray-500 font-medium">{{ $project->client->email }}</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4">Engagement</h3>
                        <div class="space-y-1">
                            <p class="text-sm font-bold text-gray-900">{{ number_format($project->total_amount, 0) }} SAR <span class="text-[10px] text-gray-400">Total Budget</span></p>
                            <p class="text-xs text-primary font-bold">Standard SLA: 14 Days</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sub-tasks List -->
            <div class="bg-white border border-gray-100 rounded-lg p-8 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-bold">Service Deliverables</h3>
                    <span class="px-3 py-1 bg-primary-light text-primary rounded-md text-[10px] font-black uppercase tracking-widest">Live Execution</span>
                </div>
                <div class="space-y-4">
                    @foreach(['Initial audit and data gathering', 'ZATCA portal registration and credential verification', 'Integration with existing ERP/Accounting software', 'Training session for internal finance team', 'Final compliance verification and certificate issuance'] as $index => $subtask)
                    <div class="flex items-center justify-between p-4 rounded-lg bg-gray-50 border border-transparent hover:border-primary/20 transition-all group">
                        <div class="flex items-center space-x-4">
                            <div class="w-6 h-6 rounded-md flex items-center justify-center border-2 {{ $index < 3 ? 'bg-primary border-primary text-white' : 'bg-white border-gray-200 text-gray-300' }}">
                                <i data-lucide="check" class="w-4 h-4"></i>
                            </div>
                            <span class="text-sm font-medium {{ $index < 3 ? 'text-gray-900' : 'text-gray-400' }}">{{ $subtask }}</span>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest {{ $index < 3 ? 'text-green-500' : 'text-gray-400' }}">
                            {{ $index < 3 ? 'Completed' : 'In Progress' }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right: Real-time Chat -->
        <div class="bg-white border border-gray-100 rounded-lg shadow-xl flex flex-col min-h-0 overflow-hidden">
            <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                <div class="flex items-center space-x-3">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <h3 class="text-sm font-black uppercase tracking-widest text-gray-900">Project Chat</h3>
                </div>
                <button class="text-gray-400 hover:text-gray-900 transition-colors">
                    <i data-lucide="more-horizontal" class="w-5 h-5"></i>
                </button>
            </div>
            
            <!-- Chat Messages -->
            <div class="flex-1 p-6 overflow-y-auto space-y-4 custom-scrollbar bg-white">
                <div class="flex flex-col space-y-1 max-w-[85%]">
                    <div class="bg-gray-100 p-4 rounded-lg rounded-tl-none border border-gray-50">
                        <p class="text-xs font-medium text-gray-700">Hey, just uploaded the integration logs. Can you take a look?</p>
                    </div>
                    <span class="text-[8px] font-black uppercase text-gray-300 ml-1">Client • 10:45 AM</span>
                </div>

                <div class="flex flex-col space-y-1 max-w-[85%] ml-auto items-end">
                    <div class="bg-primary p-4 rounded-lg rounded-tr-none text-white shadow-lg shadow-primary/20">
                        <p class="text-xs font-medium">Understood. Reviewing them now. Will update the task board shortly.</p>
                    </div>
                    <span class="text-[8px] font-black uppercase text-gray-300 mr-1">You • 10:48 AM</span>
                </div>
            </div>

            <!-- Chat Input -->
            <div class="p-4 bg-gray-50 border-t border-gray-100">
                <div class="relative">
                    <textarea placeholder="Write a message..." rows="1" class="w-full pl-4 pr-12 py-4 bg-white border border-gray-200 rounded-lg focus:ring-4 focus:ring-primary/10 outline-none transition-all font-medium text-xs resize-none shadow-sm"></textarea>
                    <button class="absolute right-2 bottom-2.5 w-8 h-8 bg-primary text-white rounded-md flex items-center justify-center hover:bg-primary-dark transition-all">
                        <i data-lucide="send" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
