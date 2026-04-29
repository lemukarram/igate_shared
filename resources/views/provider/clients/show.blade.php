@extends('layouts.app')

@section('content')
<div class="max-w-7xl w-full space-y-8 animate-in fade-in duration-700" x-data="{ lang: localStorage.getItem('igate_lang') || 'en' }">
    <div class="flex items-center gap-4">
        <a href="{{ route('provider.clients') }}" class="w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-900 transition-colors">
            <i data-lucide="arrow-left" class="w-5 h-5 flip-rtl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $client->name }}</h1>
            <p class="text-sm text-gray-500 font-medium" x-text="lang === 'ar' ? 'ملف العميل والمشاريع المرتبطة' : 'Client profile and associated projects'"></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <h2 class="text-lg font-bold text-gray-900" x-text="lang === 'ar' ? 'المشاريع المتعاقد عليها' : 'Contracted Projects'"></h2>
            <div class="grid grid-cols-1 gap-4">
                @foreach($projects as $project)
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-primary-light rounded-xl flex items-center justify-center text-primary">
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
                    <a href="{{ route('projects.show', $project->id) }}" class="px-6 py-2.5 bg-gray-50 text-gray-700 rounded-xl text-sm font-bold hover:bg-gray-100 transition-all border border-gray-100">
                        <span x-text="lang === 'ar' ? 'فتح مساحة العمل' : 'Workspace'"></span>
                    </a>
                </div>
                @endforeach
            </div>
        </div>

        <div class="space-y-6">
            <h2 class="text-lg font-bold text-gray-900" x-text="lang === 'ar' ? 'معلومات التواصل' : 'Contact Information'"></h2>
            <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-sm">
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400">
                            <i data-lucide="mail" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1" x-text="lang === 'ar' ? 'البريد الإلكتروني' : 'Email Address'"></p>
                            <p class="text-sm font-bold text-gray-900">{{ $client->email }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400">
                            <i data-lucide="phone" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1" x-text="lang === 'ar' ? 'رقم الجوال' : 'Phone Number'"></p>
                            <p class="text-sm font-bold text-gray-900">+966 50 000 0000</p>
                        </div>
                    </div>
                </div>
                <div class="mt-8 pt-8 border-t border-gray-50">
                    <button class="w-full py-3 bg-primary-light text-primary rounded-xl text-sm font-bold hover:bg-primary hover:text-white transition-all flex items-center justify-center gap-2">
                        <i data-lucide="message-circle" class="w-4 h-4"></i>
                        <span x-text="lang === 'ar' ? 'بدء محادثة مباشرة' : 'Direct Message'"></span>
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
