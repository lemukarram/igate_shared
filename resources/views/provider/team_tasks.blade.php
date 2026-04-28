@extends('layouts.app')

@section('content')
<div class="w-full max-w-7xl mx-auto space-y-8 animate-in fade-in duration-700 h-full flex flex-col relative" x-data="taskManager()">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 tracking-tight">Team Tasks</h1>
            <p class="text-gray-500 font-medium mt-1 text-xs uppercase tracking-wider">Internal Workforce Management</p>
        </div>
        <button @click="showModal = true" class="px-5 py-2.5 bg-[#3da9e4] text-white rounded-lg font-medium text-sm hover:bg-[#2b8bc2] transition-all flex items-center space-x-2 shadow-md">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Create Internal Task</span>
        </button>
    </div>

    <!-- Kanban Columns -->
    <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-6 overflow-hidden">
        <!-- Todo -->
        <div class="bg-white border border-gray-100 rounded-lg p-5 flex flex-col shadow-sm"
             @dragover.prevent="dragOverColumn = 'todo'" 
             @dragleave.prevent="dragOverColumn = null"
             @drop.prevent="dropTask('todo')"
             :class="{ 'ring-2 ring-[#3da9e4]/30 bg-gray-50/50': dragOverColumn === 'todo' }">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-sm font-semibold text-gray-700">Preparation</h3>
                <span class="w-6 h-6 bg-gray-50 border border-gray-100 rounded-md flex items-center justify-center text-xs font-medium text-gray-500">{{ isset($tasks['todo']) ? $tasks['todo']->count() : 0 }}</span>
            </div>
            <div class="space-y-4 flex-1 overflow-y-auto custom-scrollbar pr-1">
                @if(isset($tasks['todo']))
                    @foreach($tasks['todo'] as $task)
                    <div draggable="true" @dragstart="dragStart(event, {{ $task->id }})" class="bg-gray-50 p-4 rounded-lg border border-gray-100 group cursor-grab hover:border-[#3da9e4] transition-all">
                        <div class="flex items-start justify-between mb-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider 
                                {{ $task->priority === 'urgent' ? 'bg-red-50 text-red-600' : ($task->priority === 'high' ? 'bg-orange-50 text-orange-600' : 'bg-blue-50 text-blue-600') }}">
                                {{ $task->priority }}
                            </span>
                            <button class="text-gray-400 hover:text-gray-600"><i data-lucide="more-horizontal" class="w-4 h-4"></i></button>
                        </div>
                        <p class="text-sm font-medium text-gray-800 mb-3">{{ $task->title }}</p>
                        <div class="flex items-center justify-between pt-3 border-t border-gray-200/50">
                            <div class="flex -space-x-2">
                                @if($task->assignedUser)
                                <div class="w-6 h-6 rounded-full border border-white bg-[#3da9e4] text-white text-[10px] flex items-center justify-center font-medium" title="{{ $task->assignedUser->name }}">
                                    {{ substr($task->assignedUser->name, 0, 2) }}
                                </div>
                                @else
                                <div class="w-6 h-6 rounded-full border border-gray-200 bg-gray-100 text-gray-400 text-[10px] flex items-center justify-center font-medium">
                                    <i data-lucide="user" class="w-3 h-3"></i>
                                </div>
                                @endif
                            </div>
                            @if($task->due_date)
                            <div class="flex items-center text-gray-500 text-xs font-medium">
                                <i data-lucide="calendar" class="w-3 h-3 mr-1"></i>
                                {{ \Carbon\Carbon::parse($task->due_date)->format('M d') }}
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- In Progress -->
        <div class="bg-white border border-gray-100 rounded-lg p-5 flex flex-col shadow-sm"
             @dragover.prevent="dragOverColumn = 'in_progress'" 
             @dragleave.prevent="dragOverColumn = null"
             @drop.prevent="dropTask('in_progress')"
             :class="{ 'ring-2 ring-[#3da9e4]/30 bg-gray-50/50': dragOverColumn === 'in_progress' }">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-sm font-semibold text-[#3da9e4]">In Execution</h3>
                <span class="w-6 h-6 bg-[#e6f4fd] text-[#3da9e4] rounded-md flex items-center justify-center text-xs font-medium border border-[#3da9e4]/20">{{ isset($tasks['in_progress']) ? $tasks['in_progress']->count() : 0 }}</span>
            </div>
            <div class="space-y-4 flex-1 overflow-y-auto custom-scrollbar pr-1">
                @if(isset($tasks['in_progress']))
                    @foreach($tasks['in_progress'] as $task)
                    <div draggable="true" @dragstart="dragStart(event, {{ $task->id }})" class="bg-gray-50 p-4 rounded-lg border border-[#3da9e4]/30 group cursor-grab">
                        <div class="flex items-start justify-between mb-2">
                            <span class="px-2 py-0.5 bg-[#e6f4fd] text-[#3da9e4] rounded text-[10px] font-semibold uppercase tracking-wider">Active</span>
                            <i data-lucide="loader" class="w-4 h-4 text-[#3da9e4] animate-spin"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-800 mb-3">{{ $task->title }}</p>
                        <div class="flex items-center justify-between pt-3 border-t border-gray-200/50">
                            <div class="flex -space-x-2">
                                @if($task->assignedUser)
                                <div class="w-6 h-6 rounded-full border border-white bg-purple-100 text-[10px] flex items-center justify-center font-medium text-purple-700" title="{{ $task->assignedUser->name }}">
                                    {{ substr($task->assignedUser->name, 0, 2) }}
                                </div>
                                @else
                                <div class="w-6 h-6 rounded-full border border-gray-200 bg-gray-100 text-gray-400 text-[10px] flex items-center justify-center font-medium">
                                    <i data-lucide="user" class="w-3 h-3"></i>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Done -->
        <div class="bg-white border border-gray-100 rounded-lg p-5 flex flex-col shadow-sm"
             @dragover.prevent="dragOverColumn = 'done'" 
             @dragleave.prevent="dragOverColumn = null"
             @drop.prevent="dropTask('done')"
             :class="{ 'ring-2 ring-[#3da9e4]/30 bg-gray-50/50': dragOverColumn === 'done' }">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-sm font-semibold text-green-500">Validation</h3>
                <i data-lucide="check-circle" class="w-4 h-4 text-green-500"></i>
            </div>
            <div class="space-y-4 flex-1 overflow-y-auto custom-scrollbar pr-1">
                @if(isset($tasks['done']))
                    @foreach($tasks['done'] as $task)
                    <div draggable="true" @dragstart="dragStart(event, {{ $task->id }})" class="bg-gray-50 p-4 rounded-lg border border-gray-100 opacity-60 cursor-grab">
                        <p class="text-sm font-medium text-gray-700 mb-2 line-through">{{ $task->title }}</p>
                        <p class="text-xs text-gray-400 font-medium">Completed {{ $task->updated_at->diffForHumans() }}</p>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <!-- Create Task Modal -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
        <div @click.away="showModal = false" class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto border border-gray-100">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-900">Create New Task</h2>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form action="{{ route('provider.team_tasks.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Task Title *</label>
                        <input type="text" name="title" required placeholder="E.g. Review legal documents" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#3da9e4]/50 focus:border-[#3da9e4] outline-none text-sm">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Assign To</label>
                            <select name="assigned_to" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#3da9e4]/50 outline-none text-sm">
                                <option value="">Unassigned</option>
                                @if(isset($teamMembers))
                                    @foreach($teamMembers as $member)
                                    <option value="{{ $member->user->id }}">{{ $member->user->name }} ({{ ucfirst($member->role) }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deadline</label>
                            <input type="date" name="due_date" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#3da9e4]/50 outline-none text-sm text-gray-700">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                            <select name="priority" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#3da9e4]/50 outline-none text-sm">
                                <option value="normal">Normal</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#3da9e4]/50 outline-none text-sm">
                                <option value="todo">Preparation</option>
                                <option value="in_progress">In Execution</option>
                                <option value="review">Review</option>
                                <option value="done">Validation (Done)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Task Description & Comments</label>
                        <textarea name="description" rows="3" placeholder="Add task details or initial comments..." class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#3da9e4]/50 focus:border-[#3da9e4] outline-none text-sm resize-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Attachments</label>
                        <div class="border-2 border-dashed border-gray-200 rounded-lg p-6 text-center hover:bg-gray-50 transition-colors cursor-pointer">
                            <i data-lucide="upload-cloud" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
                            <p class="text-sm text-gray-500 font-medium">Click to upload multiple files or drag and drop</p>
                            <p class="text-xs text-gray-400 mt-1">PDF, DOCX, JPG up to 10MB</p>
                            <input type="file" multiple class="hidden">
                        </div>
                    </div>
                </div>
                <div class="p-6 border-t border-gray-100 flex justify-end space-x-3 bg-gray-50 rounded-b-lg">
                    <button type="button" @click="showModal = false" class="px-5 py-2.5 text-gray-600 bg-white border border-gray-200 rounded-lg font-medium text-sm hover:bg-gray-100 transition-colors shadow-sm">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 bg-[#3da9e4] text-white rounded-lg font-medium text-sm hover:bg-[#2b8bc2] transition-colors shadow-sm">Save Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('taskManager', () => ({
            showModal: false,
            draggedTaskId: null,
            dragOverColumn: null,

            dragStart(event, taskId) {
                this.draggedTaskId = taskId;
                event.dataTransfer.effectAllowed = 'move';
            },

            async dropTask(column) {
                if (!this.draggedTaskId) return;
                
                let status = column;
                
                // Optimistic UI update could go here
                
                try {
                    let response = await fetch(`/provider/team-tasks/${this.draggedTaskId}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ status: status })
                    });
                    
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        console.error("Failed to update status");
                    }
                } catch (error) {
                    console.error("Error updating status:", error);
                }
                
                this.draggedTaskId = null;
                this.dragOverColumn = null;
            }
        }));
    });
</script>
@endsection
