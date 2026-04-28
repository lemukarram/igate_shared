@extends('layouts.app')

@section('content')
<div class="w-full max-w-7xl mx-auto space-y-8 animate-in fade-in duration-700 h-full flex flex-col relative" x-data="{ showModal: false }">
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
        <div class="bg-white border border-gray-100 rounded-lg p-5 flex flex-col shadow-sm">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-sm font-semibold text-gray-700">Preparation</h3>
                <span class="w-6 h-6 bg-gray-50 border border-gray-100 rounded-md flex items-center justify-center text-xs font-medium text-gray-500">4</span>
            </div>
            <div class="space-y-4 flex-1 overflow-y-auto custom-scrollbar pr-1">
                @foreach(['Review Q2 tax filing documents', 'Update HR onboarding policy v2', 'Prepare ZATCA Phase 2 presentation', 'Coordinate with legal for NDA drafts'] as $task)
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 group cursor-pointer hover:border-[#3da9e4] transition-all">
                    <div class="flex items-start justify-between mb-2">
                        <span class="px-2 py-0.5 bg-red-50 text-red-600 rounded text-[10px] font-semibold uppercase tracking-wider">Urgent</span>
                        <i data-lucide="more-horizontal" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-800 mb-3">{{ $task }}</p>
                    <div class="flex items-center justify-between pt-3 border-t border-gray-200/50">
                        <div class="flex -space-x-2">
                            <div class="w-6 h-6 rounded-full border border-white bg-blue-100 text-[10px] flex items-center justify-center font-medium text-blue-700">SA</div>
                            <div class="w-6 h-6 rounded-full border border-white bg-green-100 text-[10px] flex items-center justify-center font-medium text-green-700">MK</div>
                        </div>
                        <div class="flex items-center text-gray-500 text-xs font-medium">
                            <i data-lucide="calendar" class="w-3 h-3 mr-1"></i>
                            Apr 28
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- In Progress -->
        <div class="bg-white border border-gray-100 rounded-lg p-5 flex flex-col shadow-sm">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-sm font-semibold text-[#3da9e4]">In Execution</h3>
                <span class="w-6 h-6 bg-[#e6f4fd] text-[#3da9e4] rounded-md flex items-center justify-center text-xs font-medium border border-[#3da9e4]/20">2</span>
            </div>
            <div class="space-y-4 flex-1 overflow-y-auto custom-scrollbar pr-1">
                <div class="bg-gray-50 p-4 rounded-lg border border-[#3da9e4]/30 group cursor-pointer">
                    <div class="flex items-start justify-between mb-2">
                        <span class="px-2 py-0.5 bg-[#e6f4fd] text-[#3da9e4] rounded text-[10px] font-semibold uppercase tracking-wider">Active</span>
                        <i data-lucide="loader" class="w-4 h-4 text-[#3da9e4] animate-spin"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-800 mb-3">Migrating client data to new ZATCA portal</p>
                    <div class="h-1.5 w-full bg-gray-200 rounded-full overflow-hidden mb-3">
                        <div class="h-full bg-[#3da9e4] w-[45%] rounded-full"></div>
                    </div>
                    <div class="flex items-center justify-between pt-3 border-t border-gray-200/50">
                        <div class="w-6 h-6 rounded-full border border-white bg-purple-100 text-[10px] flex items-center justify-center font-medium text-purple-700">JS</div>
                        <div class="text-xs font-medium text-[#3da9e4]">45% Done</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Done -->
        <div class="bg-white border border-gray-100 rounded-lg p-5 flex flex-col shadow-sm">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-sm font-semibold text-green-500">Validation</h3>
                <i data-lucide="check-circle" class="w-4 h-4 text-green-500"></i>
            </div>
            <div class="space-y-4 flex-1 overflow-y-auto custom-scrollbar pr-1">
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 opacity-60">
                    <p class="text-sm font-medium text-gray-700 mb-2 line-through">Setup internal AWS sandbox</p>
                    <p class="text-xs text-gray-400 font-medium">Completed yesterday</p>
                </div>
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
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Task Title</label>
                    <input type="text" placeholder="E.g. Review legal documents" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#3da9e4]/50 focus:border-[#3da9e4] outline-none text-sm">
                </div>
                
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assign To</label>
                        <select class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#3da9e4]/50 outline-none text-sm">
                            <option>Select Team Member</option>
                            <option>Sarah Ahmed (Manager)</option>
                            <option>Mohammad Khalid (Staff)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deadline</label>
                        <input type="date" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#3da9e4]/50 outline-none text-sm text-gray-700">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                        <select class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#3da9e4]/50 outline-none text-sm">
                            <option>Normal</option>
                            <option>High</option>
                            <option>Urgent</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#3da9e4]/50 outline-none text-sm">
                            <option>Preparation</option>
                            <option>In Execution</option>
                            <option>Validation</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Task Description & Comments</label>
                    <textarea rows="3" placeholder="Add task details or initial comments..." class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#3da9e4]/50 focus:border-[#3da9e4] outline-none text-sm resize-none"></textarea>
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
                <button @click="showModal = false" class="px-5 py-2.5 text-gray-600 bg-white border border-gray-200 rounded-lg font-medium text-sm hover:bg-gray-100 transition-colors shadow-sm">Cancel</button>
                <button @click="showModal = false" class="px-5 py-2.5 bg-[#3da9e4] text-white rounded-lg font-medium text-sm hover:bg-[#2b8bc2] transition-colors shadow-sm">Save Task</button>
            </div>
        </div>
    </div>
</div>

<!-- Alpine.js for modal toggle -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection
