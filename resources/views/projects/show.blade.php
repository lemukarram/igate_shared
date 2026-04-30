@extends('layouts.app')

@section('content')
<div class="w-full max-w-7xl mx-auto h-[calc(100vh-8rem)] flex flex-col space-y-6 animate-in fade-in duration-700" 
     x-data="projectWorkspace()">
    
    <!-- Top Header -->
    <div class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-primary-light rounded-lg flex items-center justify-center text-primary">
                <i data-lucide="{{ $project->service->icon ?? 'briefcase' }}" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl font-semibold text-gray-900 tracking-tight">{{ $project->service->name }}</h1>
                <div class="flex items-center gap-2 text-xs font-medium text-gray-500 mt-1">
                    <span class="bg-gray-100 px-2 py-0.5 rounded text-gray-700">PRJ-{{ str_pad($project->id, 5, '0', STR_PAD_LEFT) }}</span>
                    <span>•</span>
                    <button @click="statusModalOpen = true" class="text-green-600 flex items-center hover:bg-green-50 px-1.5 py-0.5 rounded transition-colors">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 me-1.5 animate-pulse"></span>
                        <span x-text="lang === 'ar' ? 'نشط' : 'Active'"></span>
                    </button>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-6 border-s border-gray-100 ps-6">
            <a href="{{ route('provider.clients.show', $project->client_id) }}" class="flex items-center gap-3 hover:bg-gray-50 p-1.5 rounded-lg transition-all">
                <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center text-white font-semibold text-sm">
                    {{ substr($project->client->name, 0, 2) }}
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900">{{ $project->client->name }}</p>
                    <p class="text-xs text-gray-500 font-medium">{{ $project->company->name ?? 'Enterprise Client' }}</p>
                </div>
            </a>
            <div class="text-end hidden sm:block">
                <p class="text-sm font-semibold text-gray-900">{{ number_format($project->total_amount, 0) }} {{ Auth::user()->role === 'provider' ? 'SAR' : 'ر.س' }}</p>
                <p class="text-xs text-gray-500 font-medium" x-text="t('project.total_budget')"></p>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="flex-1 grid grid-cols-1 lg:grid-cols-3 gap-6 min-h-0">
        
        <!-- Main Area: Chat Workspace -->
        <div class="lg:col-span-2 bg-white border border-gray-100 rounded-lg shadow-sm flex flex-col min-h-0 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div class="flex items-center gap-2">
                    <i data-lucide="message-square" class="w-4 h-4 text-primary"></i>
                    <h3 class="text-sm font-semibold text-gray-800" x-text="t('project.workspace')"></h3>
                </div>
            </div>
            
            <div class="flex-1 p-6 overflow-y-auto space-y-6 custom-scrollbar bg-white">
                @foreach($messages as $msg)
                <div class="flex gap-4">
                    <div class="w-8 h-8 rounded-full {{ $msg->user_id === Auth::id() ? 'bg-primary' : 'bg-gray-900' }} flex-shrink-0 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                        {{ substr($msg->user->name, 0, 1) }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-sm font-semibold text-gray-900">
                                @if($msg->user_id === Auth::id())
                                    <span x-text="lang === 'ar' ? 'أنت' : 'You'"></span>
                                @else
                                    {{ $msg->user->name }}
                                @endif
                            </span>
                            <span class="text-xs text-gray-400">{{ $msg->created_at->format('M d, g:i A') }}</span>
                        </div>
                        <div class="text-gray-700 text-sm leading-relaxed {{ $msg->user_id === Auth::id() ? 'bg-gray-50 p-4 rounded-lg border border-gray-100' : '' }}">
                            <p>{{ $msg->message }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Chat Input -->
            <div class="p-4 bg-white border-t border-gray-100">
                <form action="{{ route('projects.messages.store', $project->id) }}" method="POST">
                    @csrf
                    <div class="relative bg-gray-50 border border-gray-200 rounded-2xl shadow-sm focus-within:ring-2 focus-within:ring-primary/50 focus-within:border-primary transition-all">
                        <textarea name="message" required :placeholder="t('project.type_message')" rows="2" class="w-full ps-4 pe-16 py-3 bg-transparent outline-none font-medium text-sm text-gray-700 resize-none custom-scrollbar"></textarea>
                        <div class="absolute inset-y-2 end-2 flex items-center gap-1">
                            <button type="submit" class="p-2 bg-primary text-white rounded-full flex items-center justify-center hover:bg-primary-dark transition-colors shadow-md">
                                <i data-lucide="arrow-up" class="w-4 h-4 flip-rtl"></i>
                            </button>
                        </div>
                    </div>
                    <p class="text-center text-[10px] text-gray-400 mt-2 font-medium" x-text="t('project.sla_notice')"></p>
                </form>
            </div>
        </div>

        <!-- Right Side: Tasks & Progress -->
        <div class="lg:col-span-1 space-y-6 overflow-y-auto custom-scrollbar pe-1">
            
            <!-- Progress -->
            <div class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm">
                <div class="flex justify-between items-end mb-3">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1" x-text="t('project.progress')"></p>
                        @php
                            $totalTasks = $project->tasks->count();
                            $completedTasks = $project->tasks->where('status', 'done')->count();
                            $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                        @endphp
                        <h3 class="text-2xl font-bold text-gray-900">{{ $progress }}%</h3>
                    </div>
                    <div class="w-10 h-10 rounded-full border-4 border-primary-light flex items-center justify-center">
                        <i data-lucide="activity" class="w-4 h-4 text-primary"></i>
                    </div>
                </div>
                <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-primary transition-all duration-1000" style="width: {{ $progress }}%"></div>
                </div>
            </div>

            <!-- Service Tasks -->
            <div class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm flex flex-col">
                <h3 class="text-sm font-semibold text-gray-900 mb-5" x-text="t('project.service_tasks')"></h3>
                <div class="space-y-3">
                    @forelse($project->tasks as $task)
                    <div class="p-3 rounded-lg border {{ $task->status === 'done' ? 'bg-gray-50 border-gray-200' : 'bg-white border-gray-100 hover:border-primary/20' }} transition-colors cursor-pointer group" @click="openTaskModal({{ json_encode($task) }})">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-5 h-5 rounded {{ $task->status === 'done' ? 'bg-primary border-primary text-white' : 'bg-white border-gray-300 text-transparent group-hover:border-primary' }} border flex items-center justify-center transition-colors">
                                <i data-lucide="check" class="w-3 h-3"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium {{ $task->status === 'done' ? 'text-gray-500 line-through' : 'text-gray-800' }}">{{ $task->title }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 italic p-3 text-center" x-text="lang === 'ar' ? 'لا توجد مهام حالياً' : 'No tasks yet'"></p>
                    @endforelse
                </div>
            </div>

            <!-- Vault -->
            <div class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-900" x-text="t('project.vault')"></h3>
                    <i data-lucide="folder" class="w-4 h-4 text-gray-400"></i>
                </div>
                <div class="space-y-2">
                    @forelse($project->documents as $doc)
                    <div class="flex items-center justify-between p-2 rounded hover:bg-gray-50 transition-all border border-transparent hover:border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded bg-primary-light flex items-center justify-center text-primary">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-xs font-medium text-gray-800 truncate max-w-[120px]">{{ $doc->name }}</p>
                                <p class="text-[10px] text-gray-400">{{ $doc->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="text-gray-400 hover:text-primary">
                            <i data-lucide="download" class="w-3 h-3"></i>
                        </a>
                    </div>
                    @empty
                    <p class="text-[10px] text-gray-400 italic text-center py-4" x-text="lang === 'ar' ? 'لم يتم رفع ملفات بعد' : 'No files uploaded yet'"></p>
                    @endforelse
                </div>
                @if(Auth::user()->role === 'provider')
                <button onclick="document.getElementById('project-file-upload-modal').classList.remove('hidden')" class="w-full mt-3 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 border-dashed rounded-lg text-xs font-medium text-gray-600 transition-all flex items-center justify-center gap-2">
                    <i data-lucide="upload" class="w-3 h-3"></i>
                    <span x-text="lang === 'ar' ? 'رفع ملف' : 'Upload File'"></span>
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Status Change Modal -->
    <div x-show="statusModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center" style="display: none;">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="statusModalOpen = false"></div>
        <div class="bg-white w-full max-w-sm rounded-xl shadow-2xl relative z-10 p-6 border border-gray-100">
            <h2 class="text-xl font-bold mb-4" x-text="lang === 'ar' ? 'تغيير حالة المشروع' : 'Change Project Status'"></h2>
            <form action="{{ route('projects.update-status', $project->id) }}" method="POST" class="space-y-4">
                @csrf @method('PATCH')
                <div class="space-y-2">
                    <label class="flex items-center gap-3 p-3 border border-gray-100 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="status" value="active" {{ $project->status === 'active' ? 'checked' : '' }} class="w-4 h-4 text-primary">
                        <span class="text-sm font-medium" x-text="lang === 'ar' ? 'نشط' : 'Active'"></span>
                    </label>
                    <label class="flex items-center gap-3 p-3 border border-gray-100 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="status" value="inactive" {{ $project->status === 'inactive' ? 'checked' : '' }} class="w-4 h-4 text-primary">
                        <span class="text-sm font-medium" x-text="lang === 'ar' ? 'غير نشط' : 'Inactive'"></span>
                    </label>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" @click="statusModalOpen = false" class="flex-1 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-bold text-sm" x-text="lang === 'ar' ? 'إلغاء' : 'Cancel'"></button>
                    <button type="submit" class="flex-1 py-2.5 bg-primary text-white rounded-lg font-bold text-sm" x-text="lang === 'ar' ? 'حفظ التغييرات' : 'Save Changes'"></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Task Edit Modal -->
    <div x-show="taskModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center" style="display: none;">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="taskModalOpen = false"></div>
        <div class="bg-white w-full max-w-lg rounded-xl shadow-2xl relative z-10 p-8 border border-gray-100">
            <h2 class="text-2xl font-bold mb-6" x-text="lang === 'ar' ? 'تعديل المهمة' : 'Update Task Status'"></h2>
            <form :action="'/tasks/' + selectedTask.id + '/status'" method="POST" class="space-y-6">
                @csrf @method('PATCH')
                <div>
                    <label class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2 block" x-text="lang === 'ar' ? 'عنوان المهمة' : 'Task Title'"></label>
                    <p class="text-lg font-bold text-gray-900" x-text="selectedTask.title"></p>
                </div>
                <div>
                    <label class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2 block" x-text="lang === 'ar' ? 'الحالة' : 'Status'"></label>
                    <select name="status" x-model="selectedTask.status" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold outline-none focus:ring-2 focus:ring-primary/20">
                        <option value="todo">To Do</option>
                        <option value="in_progress">In Progress</option>
                        <option value="review">Review</option>
                        <option value="done">Done</option>
                    </select>
                </div>
                <div class="flex gap-4 pt-4">
                    <button type="button" @click="taskModalOpen = false" class="flex-1 py-4 bg-gray-100 text-gray-700 rounded-xl font-bold" x-text="lang === 'ar' ? 'إلغاء' : 'Cancel'"></button>
                    <button type="submit" class="flex-1 py-4 bg-primary text-white rounded-xl font-bold" x-text="lang === 'ar' ? 'حفظ التغييرات' : 'Save Changes'"></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function projectWorkspace() {
        return {
            statusModalOpen: false,
            taskModalOpen: false,
            selectedTask: {id: null, title: '', status: ''},
            openTaskModal(task) {
                this.selectedTask = {...task};
                this.taskModalOpen = true;
            },
            init() { lucide.createIcons(); }
        }
    }
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    [dir="rtl"] .flip-rtl { transform: scaleX(-1); }
</style>
@endsection
