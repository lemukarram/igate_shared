@extends('layouts.app')

@section('content')
<div class="max-w-7xl w-full space-y-8 animate-in fade-in duration-700">
    <div class="flex items-center gap-4">
        <a href="{{ route('provider.clients') }}" class="w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-900 transition-colors">
            <i data-lucide="arrow-left" class="w-5 h-5 flip-rtl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $client->name }}</h1>
            <p class="text-sm text-gray-500 font-medium" x-text="lang === 'ar' ? 'ملف العميل والمشاريع المرتبطة' : 'Client profile and associated projects'"></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="{ releaseModalOpen: false, releaseAmount: '' }">
        <div class="lg:col-span-2 space-y-8">
            <div class="space-y-4">
                <h2 class="text-lg font-bold text-gray-900" x-text="lang === 'ar' ? 'المشاريع المتعاقد عليها' : 'Contracted Projects'"></h2>
                <div class="grid grid-cols-1 gap-4">
                    @foreach($projects as $project)
                    <div class="bg-white border border-gray-100 rounded-lg p-6 shadow-sm hover:shadow-md transition-all flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-primary-light rounded-lg flex items-center justify-center text-primary">
                                <i data-lucide="{{ $project->service->icon }}" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-lg">{{ $project->service->name }}</h4>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[10px] font-black uppercase text-gray-400 tracking-widest">PRJ-{{ str_pad($project->id, 5, '0', STR_PAD_LEFT) }}</span>
                                    <span class="text-[10px] font-black uppercase text-green-500 bg-green-50 px-2 py-0.5 rounded-md" x-text="lang === 'ar' ? 'نشط' : 'Active'"></span>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <button @click="releaseModalOpen = true; $dispatch('set-project', {id: {{ $project->id }}, name: '{{ $project->service->name }}'})" class="px-4 py-2.5 bg-blue-50 text-blue-600 rounded-lg text-sm font-bold hover:bg-blue-100 transition-all border border-blue-100">
                                <span x-text="lang === 'ar' ? 'طلب تحويل' : 'Release Schedule'"></span>
                            </button>
                            <a href="{{ route('projects.show', $project->id) }}" class="px-6 py-2.5 bg-gray-900 text-white rounded-lg text-sm font-bold hover:bg-black transition-all">
                                <span x-text="lang === 'ar' ? 'فتح مساحة العمل' : 'Workspace'"></span>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="space-y-4">
                <h2 class="text-lg font-bold text-gray-900" x-text="lang === 'ar' ? 'النشاطات الأخيرة' : 'Recent Activities'"></h2>
                <div class="bg-white border border-gray-100 rounded-lg overflow-hidden">
                    <div class="divide-y divide-gray-50">
                        @forelse($activities as $activity)
                        <div class="p-4 flex items-start gap-4 hover:bg-gray-50 transition-all">
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mt-1">
                                <i data-lucide="activity" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $activity->description }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="p-12 text-center text-gray-400 italic" x-text="lang === 'ar' ? 'لا توجد نشاطات مسجلة.' : 'No activities recorded.'"></div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <h2 class="text-lg font-bold text-gray-900" x-text="lang === 'ar' ? 'معلومات التواصل' : 'Contact Information'"></h2>
            <div class="bg-white border border-gray-100 rounded-lg p-8 shadow-sm">
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center text-gray-400">
                            <i data-lucide="mail" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1" x-text="lang === 'ar' ? 'البريد الإلكتروني' : 'Email Address'"></p>
                            <p class="text-sm font-bold text-gray-900">{{ $client->email }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center text-gray-400">
                            <i data-lucide="phone" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1" x-text="lang === 'ar' ? 'رقم الجوال' : 'Phone Number'"></p>
                            <p class="text-sm font-bold text-gray-900">{{ $client->phone ?? '+966 50 000 0000' }}</p>
                        </div>
                    </div>
                </div>
                <div class="mt-8 pt-8 border-t border-gray-50">
                    <a href="{{ route('projects.show', $projects->first()->id ?? 0) }}" class="w-full py-3 bg-primary text-white rounded-lg text-sm font-bold hover:bg-primary-dark transition-all flex items-center justify-center gap-2">
                        <i data-lucide="message-circle" class="w-4 h-4"></i>
                        <span x-text="lang === 'ar' ? 'بدء محادثة مباشرة' : 'Chat Now'"></span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Release Request Modal -->
        <div x-show="releaseModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center" style="display: none;" 
             @set-project.window="selectedProject = $event.detail">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="releaseModalOpen = false"></div>
            <div class="bg-white w-full max-w-md rounded-xl shadow-2xl relative z-10 p-8 border border-gray-100">
                <h2 class="text-2xl font-bold mb-2" x-text="lang === 'ar' ? 'طلب تحرير دفعات' : 'Release Request'"></h2>
                <p class="text-gray-500 text-sm mb-6" x-text="'Project: ' + selectedProject.name"></p>
                <form action="{{ route('provider.release-requests.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="project_id" :value="selectedProject.id">
                    <div>
                        <label class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2 block" x-text="lang === 'ar' ? 'المبلغ' : 'Amount (SAR)'"></label>
                        <input type="number" name="amount" required step="0.01" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-lg text-sm font-bold outline-none">
                    </div>
                    <div>
                        <label class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2 block" x-text="lang === 'ar' ? 'ملاحظات' : 'Notes'"></label>
                        <textarea name="notes" rows="3" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-lg text-sm font-medium outline-none"></textarea>
                    </div>
                    <div class="flex gap-4 pt-4">
                        <button type="button" @click="releaseModalOpen = false" class="flex-1 py-3 bg-gray-100 text-gray-700 rounded-lg font-bold" x-text="lang === 'ar' ? 'إلغاء' : 'Cancel'"></button>
                        <button type="submit" class="flex-1 py-3 bg-primary text-white rounded-lg font-bold" x-text="lang === 'ar' ? 'إرسال الطلب' : 'Release Request'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('clientProfile', () => ({
            selectedProject: {id: null, name: ''}
        }))
    })
</script>

<style>
    [dir="rtl"] .flip-rtl { transform: scaleX(-1); }
</style>
@endsection
