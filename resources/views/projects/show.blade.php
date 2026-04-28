@extends('layouts.app')

@section('content')
<div class="w-full max-w-7xl mx-auto h-[calc(100vh-8rem)] flex flex-col space-y-6 animate-in fade-in duration-700">
    
    <!-- Top Header: Client & Project Info -->
    <div class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-[#e6f4fd] rounded-lg flex items-center justify-center text-[#3da9e4]">
                <i data-lucide="{{ $project->service->icon ?? 'briefcase' }}" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl font-semibold text-gray-900 tracking-tight">{{ $project->service->name }}</h1>
                <div class="flex items-center space-x-2 text-xs font-medium text-gray-500 mt-1">
                    <span class="bg-gray-100 px-2 py-0.5 rounded text-gray-700">PRJ-{{ str_pad($project->id, 5, '0', STR_PAD_LEFT) }}</span>
                    <span>•</span>
                    <span class="text-green-600 flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5 animate-pulse"></span>Active</span>
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-6 border-l border-gray-100 pl-6">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center text-white font-semibold text-sm">
                    {{ substr($project->client->name, 0, 2) }}
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900">{{ $project->client->name }}</p>
                    <p class="text-xs text-gray-500 font-medium">{{ $project->company->name ?? 'Enterprise Client' }}</p>
                </div>
            </div>
            <div class="text-right hidden sm:block">
                <p class="text-sm font-semibold text-gray-900">{{ number_format($project->total_amount, 0) }} SAR</p>
                <p class="text-xs text-gray-500 font-medium">Total Budget</p>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="flex-1 grid grid-cols-1 lg:grid-cols-3 gap-6 min-h-0">
        
        <!-- Main Area: AI Style Chat (lg:col-span-2) -->
        <div class="lg:col-span-2 bg-white border border-gray-100 rounded-lg shadow-sm flex flex-col min-h-0 overflow-hidden">
            <!-- Chat Header -->
            <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div class="flex items-center space-x-2">
                    <i data-lucide="message-square" class="w-4 h-4 text-[#3da9e4]"></i>
                    <h3 class="text-sm font-semibold text-gray-800">Project Workspace</h3>
                </div>
                <div class="flex space-x-2">
                    <button class="p-1.5 text-gray-400 hover:text-[#3da9e4] transition-colors rounded-md hover:bg-[#e6f4fd]">
                        <i data-lucide="paperclip" class="w-4 h-4"></i>
                    </button>
                    <button class="p-1.5 text-gray-400 hover:text-gray-900 transition-colors rounded-md hover:bg-gray-100">
                        <i data-lucide="more-vertical" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
            
            <!-- Chat Messages (AI Style) -->
            <div class="flex-1 p-6 overflow-y-auto space-y-6 custom-scrollbar bg-white">
                <!-- System/AI welcome message -->
                <div class="flex space-x-4">
                    <div class="w-8 h-8 rounded-full bg-[#e6f4fd] flex-shrink-0 flex items-center justify-center border border-[#3da9e4]/20">
                        <img src="/images/logo/icon.png" onerror="this.src='data:image/svg+xml;base64,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%233da9e4%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><path d=%22M12 2v20%22/><path d=%22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6%22/></svg>'" class="w-5 h-5 object-contain" alt="AI">
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-1">
                            <span class="text-sm font-semibold text-gray-900">iGate System</span>
                            <span class="text-xs text-gray-400">{{ $project->created_at->format('M d, g:i A') }}</span>
                        </div>
                        <div class="text-gray-700 text-sm leading-relaxed prose prose-sm max-w-none">
                            <p>Welcome to the project workspace. The escrow has been funded and the SLA timer has started. You can use this space to communicate, share files, and update milestones.</p>
                        </div>
                    </div>
                </div>

                @foreach($messages as $msg)
                <div class="flex space-x-4">
                    @if($msg->user_id === Auth::id())
                    <div class="w-8 h-8 rounded-full bg-[#3da9e4] flex-shrink-0 flex items-center justify-center text-white text-xs font-bold shadow-md shadow-[#3da9e4]/30">
                        {{ substr($msg->user->name, 0, 1) }}
                    </div>
                    @else
                    <div class="w-8 h-8 rounded-full bg-gray-900 flex-shrink-0 flex items-center justify-center text-white text-xs font-bold">
                        {{ substr($msg->user->name, 0, 1) }}
                    </div>
                    @endif
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-1">
                            <span class="text-sm font-semibold text-gray-900">{{ $msg->user_id === Auth::id() ? 'You' : $msg->user->name }}</span>
                            <span class="text-xs text-gray-400">{{ $msg->created_at->format('M d, g:i A') }}</span>
                        </div>
                        <div class="text-gray-700 text-sm leading-relaxed {{ $msg->user_id === Auth::id() ? 'bg-gray-50 p-4 rounded-lg border border-gray-100' : '' }}">
                            <p>{{ $msg->message }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Chat Input (Gemini Style) -->
            <div class="p-4 bg-white border-t border-gray-100">
                <form action="{{ route('projects.messages.store', $project->id) }}" method="POST">
                    @csrf
                    <div class="relative bg-gray-50 border border-gray-200 rounded-2xl shadow-sm focus-within:ring-2 focus-within:ring-[#3da9e4]/50 focus-within:border-[#3da9e4] transition-all">
                        <textarea name="message" required placeholder="Type your message here..." rows="2" class="w-full pl-4 pr-16 py-3 bg-transparent outline-none font-medium text-sm text-gray-700 resize-none custom-scrollbar" style="max-height: 150px;"></textarea>
                        <div class="absolute right-2 bottom-2 flex items-center space-x-1">
                            <button type="button" class="p-2 text-gray-400 hover:text-gray-700 transition-colors rounded-full hover:bg-gray-200">
                                <i data-lucide="mic" class="w-4 h-4"></i>
                            </button>
                            <button type="submit" class="p-2 bg-[#3da9e4] text-white rounded-full flex items-center justify-center hover:bg-[#2b8bc2] transition-colors shadow-md disabled:opacity-50">
                                <i data-lucide="arrow-up" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                    <p class="text-center text-[10px] text-gray-400 mt-2 font-medium">All communications are recorded and monitored for SLA compliance.</p>
                </form>
            </div>
        </div>

        <!-- Right Side: Sub-tasks and Progress (lg:col-span-1) -->
        <div class="lg:col-span-1 space-y-6 overflow-y-auto custom-scrollbar pr-1">
            
            <!-- Progress Summary -->
            <div class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm">
                <div class="flex justify-between items-end mb-3">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Project Progress</p>
                        <h3 class="text-2xl font-bold text-gray-900">40%</h3>
                    </div>
                    <div class="w-10 h-10 rounded-full border-4 border-[#e6f4fd] flex items-center justify-center relative">
                        <svg class="absolute inset-0 w-full h-full text-[#3da9e4] -rotate-90" viewBox="0 0 36 36">
                            <path class="stroke-current" stroke-dasharray="100 100" stroke-dashoffset="60" stroke-width="4" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <i data-lucide="activity" class="w-4 h-4 text-[#3da9e4]"></i>
                    </div>
                </div>
                <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-[#3da9e4] w-[40%] rounded-full transition-all duration-1000"></div>
                </div>
            </div>

            <!-- Sub-tasks / Deliverables -->
            <div class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm flex flex-col">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-sm font-semibold text-gray-900">Service Tasks</h3>
                    <button class="text-xs text-[#3da9e4] font-medium hover:underline">Edit</button>
                </div>
                
                <div class="space-y-3">
                    @forelse($project->tasks as $task)
                    <div class="p-3 rounded-lg border {{ $task->status === 'done' ? 'bg-gray-50 border-gray-200' : 'bg-white border-gray-100 hover:border-[#3da9e4]/30' }} transition-colors group">
                        <div class="flex items-start space-x-3">
                            <form action="{{ route('tasks.updateStatus', $task->id) }}" method="POST" class="mt-0.5">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ $task->status === 'done' ? 'todo' : 'done' }}">
                                <button type="submit" class="flex-shrink-0 w-5 h-5 rounded {{ $task->status === 'done' ? 'bg-[#3da9e4] border-[#3da9e4] text-white' : 'bg-white border-gray-300 text-transparent hover:border-[#3da9e4]' }} border flex items-center justify-center transition-colors">
                                    <i data-lucide="check" class="w-3 h-3"></i>
                                </button>
                            </form>
                            <div class="flex-1">
                                <p class="text-sm font-medium {{ $task->status === 'done' ? 'text-gray-500 line-through' : 'text-gray-800' }}">{{ $task->title }}</p>
                                <div class="mt-2 flex items-center justify-between">
                                    <span class="text-[10px] font-semibold uppercase tracking-wider {{ $task->status === 'done' ? 'text-green-500' : 'text-gray-400' }}">
                                        {{ $task->status === 'done' ? 'Completed' : 'Pending' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 italic p-3 text-center">No tasks defined yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- Add Task Form -->
            @if(Auth::user()->role === 'provider')
            <div class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Add New Task</h3>
                <form action="{{ route('tasks.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <input type="hidden" name="status" value="todo">
                    <input type="text" name="title" required placeholder="Task description..." class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-[#3da9e4]/50 focus:border-[#3da9e4] outline-none text-sm">
                    <button type="submit" class="w-full py-2 bg-[#3da9e4] text-white rounded-md text-xs font-semibold hover:bg-[#2b8bc2] transition-colors">Add Task</button>
                </form>
            </div>
            @endif
            
            <!-- Vault/Files widget -->
            <div class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-900">Project Files</h3>
                    <i data-lucide="folder" class="w-4 h-4 text-gray-400"></i>
                </div>
                <div class="space-y-2">
                    <div class="flex items-center justify-between p-2 rounded hover:bg-gray-50 cursor-pointer transition-colors border border-transparent hover:border-gray-100">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded bg-red-50 flex items-center justify-center text-red-500">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-800">Commercial_Register.pdf</p>
                                <p class="text-[10px] text-gray-400">2.4 MB • 2 hrs ago</p>
                            </div>
                        </div>
                        <i data-lucide="download" class="w-3 h-3 text-gray-400 hover:text-[#3da9e4]"></i>
                    </div>
                </div>
                <button class="w-full mt-3 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 border-dashed rounded-lg text-xs font-medium text-gray-600 transition-colors flex items-center justify-center space-x-2">
                    <i data-lucide="upload" class="w-3 h-3"></i>
                    <span>Upload File</span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom scrollbar for chat area to make it look cleaner */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #e5e7eb;
        border-radius: 20px;
    }
    .custom-scrollbar:hover::-webkit-scrollbar-thumb {
        background-color: #d1d5db;
    }
</style>
@endsection