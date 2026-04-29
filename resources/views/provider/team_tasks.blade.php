@extends('layouts.app')

@section('content')
<div class="w-full max-w-7xl mx-auto space-y-8 animate-in fade-in duration-700 h-full flex flex-col relative" 
     x-data="taskManager()">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 tracking-tight" x-text="t('common.team')"></h1>
            <p class="text-gray-500 font-medium mt-1 text-xs uppercase tracking-wider" 
               x-text="lang === 'ar' ? 'إدارة القوى العاملة الداخلية' : 'Internal Workforce Management'"></p>
        </div>
        <button @click="openCreateModal()" class="px-5 py-2.5 bg-[#3da9e4] text-white rounded-lg font-medium text-sm hover:bg-[#2b8bc2] transition-all flex items-center gap-2 shadow-md">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span x-text="lang === 'ar' ? 'إنشاء مهمة داخلية' : 'Create Internal Task'"></span>
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
                <h3 class="text-sm font-semibold text-gray-700" x-text="t('tasks.todo')"></h3>
                <span class="w-6 h-6 bg-gray-50 border border-gray-100 rounded-md flex items-center justify-center text-xs font-medium text-gray-500">{{ isset($tasks['todo']) ? $tasks['todo']->count() : 0 }}</span>
            </div>
            <div class="space-y-4 flex-1 overflow-y-auto custom-scrollbar pe-1">
                @if(isset($tasks['todo']))
                    @foreach($tasks['todo'] as $task)
                    <div draggable="true" @dragstart="dragStart(event, {{ $task->id }})" 
                         @click="openEditModal({{ $task->id }})"
                         class="bg-gray-50 p-4 rounded-lg border border-gray-100 group cursor-grab hover:border-[#3da9e4] transition-all">
                        <div class="flex items-start justify-between mb-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider 
                                {{ $task->priority === 'urgent' ? 'bg-red-50 text-red-600' : ($task->priority === 'high' ? 'bg-orange-50 text-orange-600' : 'bg-blue-50 text-blue-600') }}"
                                x-text="t('tasks.' + '{{ $task->priority }}')">
                            </span>
                            <button class="text-gray-400 hover:text-gray-600"><i data-lucide="more-horizontal" class="w-4 h-4"></i></button>
                        </div>
                        <p class="text-sm font-medium text-gray-800 mb-3">{{ $task->title }}</p>
                        <div class="flex items-center justify-between pt-3 border-t border-gray-200/50">
                            <div class="flex -space-x-2 rtl:space-x-reverse">
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
                            <div class="flex items-center text-gray-500 text-xs font-medium gap-1">
                                <i data-lucide="calendar" class="w-3 h-3"></i>
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
                <h3 class="text-sm font-semibold text-[#3da9e4]" x-text="t('tasks.in_progress')"></h3>
                <span class="w-6 h-6 bg-[#e6f4fd] text-[#3da9e4] rounded-md flex items-center justify-center text-xs font-medium border border-[#3da9e4]/20">{{ isset($tasks['in_progress']) ? $tasks['in_progress']->count() : 0 }}</span>
            </div>
            <div class="space-y-4 flex-1 overflow-y-auto custom-scrollbar pe-1">
                @if(isset($tasks['in_progress']))
                    @foreach($tasks['in_progress'] as $task)
                    <div draggable="true" @dragstart="dragStart(event, {{ $task->id }})" 
                         @click="openEditModal({{ $task->id }})"
                         class="bg-gray-50 p-4 rounded-lg border border-[#3da9e4]/30 group cursor-grab">
                        <div class="flex items-start justify-between mb-2">
                            <span class="px-2 py-0.5 bg-[#e6f4fd] text-[#3da9e4] rounded text-[10px] font-semibold uppercase tracking-wider" 
                                  x-text="lang === 'ar' ? 'نشط' : 'Active'"></span>
                            <i data-lucide="loader" class="w-4 h-4 text-[#3da9e4] animate-spin"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-800 mb-3">{{ $task->title }}</p>
                        <div class="flex items-center justify-between pt-3 border-t border-gray-200/50">
                            <div class="flex -space-x-2 rtl:space-x-reverse">
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
                <h3 class="text-sm font-semibold text-green-500" x-text="t('tasks.done')"></h3>
                <i data-lucide="check-circle" class="w-4 h-4 text-green-500"></i>
            </div>
            <div class="space-y-4 flex-1 overflow-y-auto custom-scrollbar pe-1">
                @if(isset($tasks['done']))
                    @foreach($tasks['done'] as $task)
                    <div draggable="true" @dragstart="dragStart(event, {{ $task->id }})" 
                         @click="openEditModal({{ $task->id }})"
                         class="bg-gray-50 p-4 rounded-lg border border-gray-100 opacity-60 cursor-grab">
                        <p class="text-sm font-medium text-gray-700 mb-2 line-through">{{ $task->title }}</p>
                        <p class="text-xs text-gray-400 font-medium" x-text="(lang === 'ar' ? 'تم الإكمال ' : 'Completed ') + '{{ $task->updated_at->diffForHumans() }}'"></p>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <!-- Task Modal (Create/Edit) -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
        <div @click.away="showModal = false" class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden border border-gray-100 flex flex-col md:flex-row">
            <!-- Left Side: Form -->
            <div class="flex-1 overflow-y-auto custom-scrollbar border-e border-gray-100">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h2 class="text-xl font-semibold text-gray-900" 
                        x-text="editMode ? (lang === 'ar' ? 'تعديل المهمة' : 'Edit Task') : (lang === 'ar' ? 'إنشاء مهمة جديدة' : 'Create New Task')"></h2>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <form :action="editMode ? `/provider/team-tasks/${currentTask.id}` : '{{ route('provider.team_tasks.store') }}'" method="POST" enctype="multipart/form-data">
                    @csrf
                    <template x-if="editMode">
                        @method('PATCH')
                    </template>

                    <div class="p-6 space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" x-text="lang === 'ar' ? 'عنوان المهمة *' : 'Task Title *'"></label>
                            <input type="text" name="title" x-model="formData.title" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#3da9e4]/50 outline-none text-sm">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1" x-text="t('tasks.assigned_to')"></label>
                                <select name="assigned_to" x-model="formData.assigned_to" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#3da9e4]/50 outline-none text-sm">
                                    <option value="" x-text="lang === 'ar' ? 'غير مكلف' : 'Unassigned'"></option>
                                    @if(isset($teamMembers))
                                        @foreach($teamMembers as $member)
                                        <option value="{{ $member->user->id }}">{{ $member->user->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1" x-text="lang === 'ar' ? 'الموعد النهائي' : 'Deadline'"></label>
                                <input type="date" name="due_date" x-model="formData.due_date" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#3da9e4]/50 outline-none text-sm text-gray-700">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1" x-text="t('common.priority')"></label>
                                <select name="priority" x-model="formData.priority" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#3da9e4]/50 outline-none text-sm">
                                    <option value="normal" x-text="t('tasks.normal')"></option>
                                    <option value="high" x-text="t('tasks.high')"></option>
                                    <option value="urgent" x-text="t('tasks.urgent')"></option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1" x-text="t('common.status')"></label>
                                <select name="status" x-model="formData.status" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#3da9e4]/50 outline-none text-sm">
                                    <option value="todo" x-text="t('tasks.todo')"></option>
                                    <option value="in_progress" x-text="t('tasks.in_progress')"></option>
                                    <option value="review" x-text="t('tasks.review')"></option>
                                    <option value="done" x-text="t('tasks.done')"></option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" x-text="lang === 'ar' ? 'وصف المهمة والملاحظات' : 'Task Description & Comments'"></label>
                            <textarea name="description" x-model="formData.description" rows="3" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#3da9e4]/50 outline-none text-sm resize-none"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" x-text="lang === 'ar' ? 'المرفقات' : 'Attachments'"></label>
                            <div @click="$refs.fileInput.click()" class="border-2 border-dashed border-gray-200 rounded-lg p-6 text-center hover:bg-gray-50 transition-colors cursor-pointer">
                                <i data-lucide="upload-cloud" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
                                <p class="text-sm text-gray-500 font-medium" x-text="lang === 'ar' ? 'اضغط لرفع ملفات متعددة أو اسحب وأفلت' : 'Click to upload multiple files or drag and drop'"></p>
                                <input type="file" name="files[]" multiple x-ref="fileInput" class="hidden">
                            </div>
                        </div>
                    </div>
                    <div class="p-6 border-t border-gray-100 flex justify-end gap-3 bg-gray-50">
                        <button type="button" @click="showModal = false" class="px-5 py-2.5 text-gray-600 bg-white border border-gray-200 rounded-lg font-medium text-sm hover:bg-gray-100" x-text="t('common.cancel')"></button>
                        <button type="submit" class="px-5 py-2.5 bg-[#3da9e4] text-white rounded-lg font-medium text-sm hover:bg-[#2b8bc2]" x-text="editMode ? (lang === 'ar' ? 'تحديث' : 'Update') : (lang === 'ar' ? 'حفظ' : 'Save')"></button>
                    </div>
                </form>
            </div>

            <!-- Right Side: History -->
            <div class="w-full md:w-72 bg-gray-50/50 overflow-y-auto custom-scrollbar p-6" x-show="editMode">
                <h3 class="text-sm font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <i data-lucide="history" class="w-4 h-4 text-[#3da9e4]"></i>
                    <span x-text="t('project.history')"></span>
                </h3>
                <div class="space-y-6 relative before:absolute before:start-2 before:top-2 before:bottom-2 before:w-px before:bg-gray-200">
                    <template x-for="history in currentTask.histories" :key="history.id">
                        <div class="relative ps-6">
                            <div class="absolute start-0 top-1.5 w-4 h-4 rounded-full bg-white border-2 border-[#3da9e4] z-10"></div>
                            <div class="space-y-1">
                                <p class="text-xs text-gray-900 font-bold" x-text="history.user.name"></p>
                                <p class="text-[10px] text-gray-500 font-medium" x-text="history.action === 'created' ? (lang === 'ar' ? 'أنشأ هذه المهمة' : 'Created this task') : (history.field + (lang === 'ar' ? ' تغير إلى ' : ' changed to ') + history.new_value)"></p>
                                <p class="text-[8px] text-gray-400 font-medium italic" x-text="formatDate(history.created_at)"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function taskManager() {
        return {
            lang: localStorage.getItem('igate_lang') || 'en',
            showModal: false,
            editMode: false,
            draggedTaskId: null,
            dragOverColumn: null,
            currentTask: {},
            formData: { title: '', description: '', assigned_to: '', due_date: '', priority: 'normal', status: 'todo' },
            dict: {
                en: {
                    common: { team: "Team Tasks", priority: "Priority", status: "Status", cancel: "Cancel" },
                    tasks: { todo: "Preparation", in_progress: "Execution", review: "Review", done: "Validation", urgent: "Urgent", high: "High", normal: "Normal", assigned_to: "Assigned To" },
                    project: { history: "Task History" }
                },
                ar: {
                    common: { team: "مهام الفريق", priority: "الأولوية", status: "الحالة", cancel: "إلغاء" },
                    tasks: { todo: "تحضير", in_progress: "تنفيذ", review: "مراجعة", done: "اعتماد", urgent: "عاجل", high: "مرتفع", normal: "عادي", assigned_to: "مكلف إلى" },
                    project: { history: "سجل التحديثات" }
                }
            },
            t(key) {
                const keys = key.split('.');
                let result = this.dict[this.lang];
                for (const k of keys) result = result ? result[k] : null;
                return result || key;
            },
            openCreateModal() {
                this.editMode = false;
                this.formData = { title: '', description: '', assigned_to: '', due_date: '', priority: 'normal', status: 'todo' };
                this.showModal = true;
                this.$nextTick(() => lucide.createIcons());
            },
            async openEditModal(taskId) {
                const res = await fetch(`/provider/team-tasks/${taskId}`);
                if (res.ok) {
                    this.currentTask = await res.json();
                    this.formData = { ...this.currentTask };
                    this.editMode = true;
                    this.showModal = true;
                    this.$nextTick(() => lucide.createIcons());
                }
            },
            formatDate(d) { return new Date(d).toLocaleString(this.lang === 'ar' ? 'ar-SA' : 'en-US', { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' }); },
            dragStart(e, id) { this.draggedTaskId = id; e.dataTransfer.effectAllowed = 'move'; },
            async dropTask(col) {
                if (!this.draggedTaskId) return;
                const res = await fetch(`/provider/team-tasks/${this.draggedTaskId}/status`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ status: col })
                });
                if (res.ok) window.location.reload();
            }
        };
    }
</script>
@endsection
