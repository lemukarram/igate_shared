@extends('layouts.app')

@section('content')
<div class="w-full max-w-7xl mx-auto space-y-8 animate-in fade-in duration-700 h-full flex flex-col">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Team Tasks</h1>
            <p class="text-gray-500 font-medium mt-1 text-sm uppercase tracking-widest">Internal Workforce Management</p>
        </div>
        <button class="px-6 py-3 bg-gray-900 text-white rounded-lg font-bold text-sm hover:bg-black transition-all flex items-center space-x-2 shadow-lg shadow-gray-200">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Create Internal Task</span>
        </button>
    </div>

    <!-- Kanban Columns -->
    <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-6 overflow-hidden">
        <!-- Todo -->
        <div class="bg-gray-50 rounded-lg p-6 flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-gray-400">Preparation</h3>
                <span class="w-6 h-6 bg-white border border-gray-100 rounded-md flex items-center justify-center text-[10px] font-bold text-gray-400">4</span>
            </div>
            <div class="space-y-4 flex-1 overflow-y-auto custom-scrollbar pr-2">
                @foreach(['Review Q2 tax filing documents', 'Update HR onboarding policy v2', 'Prepare ZATCA Phase 2 presentation', 'Coordinate with legal for NDA drafts'] as $task)
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100 group cursor-pointer hover:border-primary transition-all">
                    <div class="flex items-start justify-between mb-3">
                        <span class="px-2 py-0.5 bg-blue-50 text-blue-600 rounded-md text-[8px] font-black uppercase tracking-widest">Urgent</span>
                        <i data-lucide="more-horizontal" class="w-4 h-4 text-gray-300"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-700 mb-4">{{ $task }}</p>
                    <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                        <div class="flex -space-x-2">
                            <div class="w-6 h-6 rounded-full border-2 border-white bg-gray-200"></div>
                            <div class="w-6 h-6 rounded-full border-2 border-white bg-gray-300"></div>
                        </div>
                        <div class="flex items-center text-gray-400 text-[10px] font-bold">
                            <i data-lucide="calendar" class="w-3 h-3 mr-1"></i>
                            Apr 28
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- In Progress -->
        <div class="bg-gray-50 rounded-lg p-6 flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-primary">In Execution</h3>
                <span class="w-6 h-6 bg-primary text-white rounded-md flex items-center justify-center text-[10px] font-bold">2</span>
            </div>
            <div class="space-y-4 flex-1 overflow-y-auto custom-scrollbar pr-2">
                <div class="bg-white p-5 rounded-lg shadow-sm border border-primary/20 group cursor-pointer">
                    <div class="flex items-start justify-between mb-3">
                        <span class="px-2 py-0.5 bg-primary-light text-primary rounded-md text-[8px] font-black uppercase tracking-widest">Active</span>
                        <i data-lucide="loader" class="w-4 h-4 text-primary animate-spin"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-900 mb-4">Migrating client data to new ZATCA portal</p>
                    <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden mb-4">
                        <div class="h-full bg-primary w-[45%] rounded-full"></div>
                    </div>
                    <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                        <div class="w-6 h-6 rounded-full border-2 border-white bg-primary text-white text-[8px] flex items-center justify-center font-black">SA</div>
                        <div class="text-[10px] font-bold text-primary">45% Done</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Done -->
        <div class="bg-gray-50 rounded-lg p-6 flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-green-500">Validation</h3>
                <i data-lucide="check-circle" class="w-4 h-4 text-green-500"></i>
            </div>
            <div class="space-y-4 flex-1 overflow-y-auto custom-scrollbar pr-2">
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100 opacity-60 grayscale">
                    <p class="text-sm font-medium text-gray-700 mb-2 line-through">Setup internal AWS sandbox</p>
                    <p class="text-[10px] text-gray-400 font-bold">Completed yesterday</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
