@extends('layouts.app')

@section('content')
<div class="max-w-7xl w-full space-y-8 animate-in fade-in duration-700" x-data="{ lang: localStorage.getItem('igate_lang') || 'en' }">
    <div class="flex items-center gap-4">
        <a href="{{ route('client.portfolio') }}" class="w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-900 transition-colors">
            <i data-lucide="arrow-left" class="w-5 h-5 flip-rtl"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $company->name }}</h1>
            <p class="text-sm text-gray-500 font-medium" x-text="lang === 'ar' ? 'إدارة مشاريع الشركة وملفها التعريفي' : 'Manage company projects and profile'"></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900" x-text="lang === 'ar' ? 'المشاريع' : 'Projects'"></h2>
                <a href="{{ route('explore.index') }}" class="px-4 py-2 bg-primary text-white rounded-xl text-sm font-bold hover:bg-primary-dark transition-all flex items-center gap-2 shadow-md">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span x-text="lang === 'ar' ? 'طلب خدمة' : 'Request Service'"></span>
                </a>
            </div>

            <div class="grid grid-cols-1 gap-4">
                @forelse($company->projects as $project)
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-primary-light rounded-xl flex items-center justify-center text-primary">
                            <i data-lucide="{{ $project->service->icon }}" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">{{ $project->service->name }}</h4>
                            <p class="text-xs text-gray-400 font-medium">{{ $project->provider->providerProfile->company_name ?? 'iGate Partner' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-[10px] font-black uppercase text-green-500 bg-green-50 px-2 py-0.5 rounded-md" x-text="lang === 'ar' ? 'نشط' : 'Active'"></span>
                        <a href="{{ route('projects.show', $project->id) }}" class="text-primary hover:text-primary-dark transition-colors">
                            <i data-lucide="chevron-right" class="w-5 h-5 flip-rtl"></i>
                        </a>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center bg-gray-50 rounded-3xl border border-dashed border-gray-200">
                    <p class="text-gray-500 font-medium" x-text="lang === 'ar' ? 'لا توجد مشاريع لهذه الشركة بعد.' : 'No projects for this company yet.'"></p>
                </div>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <h2 class="text-xl font-bold text-gray-900" x-text="lang === 'ar' ? 'ملف الشركة' : 'Company Profile'"></h2>
            <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-sm">
                <div class="space-y-6">
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1" x-text="lang === 'ar' ? 'القطاع' : 'Industry'"></p>
                        <p class="text-sm font-bold text-gray-900">{{ $company->industry ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1" x-text="lang === 'ar' ? 'تاريخ الإنشاء' : 'Created Date'"></p>
                        <p class="text-sm font-bold text-gray-900">{{ $company->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
                <div class="mt-8 pt-8 border-t border-gray-50">
                    <button class="w-full py-3 bg-gray-50 text-gray-700 rounded-xl text-sm font-bold hover:bg-gray-100 transition-all flex items-center justify-center gap-2">
                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                        <span x-text="lang === 'ar' ? 'تعديل الملف' : 'Edit Profile'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [dir="rtl"] .flip-rtl { transform: scaleX(-1); }
</style>
@endsection
