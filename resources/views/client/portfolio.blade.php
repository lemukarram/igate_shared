@extends('layouts.app')

@section('content')
<div class="max-w-7xl w-full space-y-8 animate-in fade-in duration-700">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900" x-text="lang === 'ar' ? 'ملفي المهني وشركاتي' : 'My Portfolio & Companies'"></h1>
            <p class="text-gray-500 mt-1" x-text="lang === 'ar' ? 'إدارة ملفات شركاتك والمشاريع المرتبطة بها.' : 'Manage your business profiles and track project history.'"></p>
        </div>
        <button onclick="document.getElementById('add-company-modal').classList.remove('hidden')" class="px-6 py-3 bg-primary text-white rounded-xl font-bold hover:bg-primary-dark transition-all flex items-center gap-2 shadow-lg shadow-primary/20">
            <i data-lucide="plus" class="w-5 h-5"></i>
            <span x-text="lang === 'ar' ? 'إضافة شركة' : 'Add Company'"></span>
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($companies as $company)
        <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
            <div class="flex items-center justify-between mb-8">
                <div class="w-16 h-16 bg-primary-light rounded-2xl flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                    <i data-lucide="building-2" class="w-8 h-8"></i>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('companies.show', $company->id) }}" class="p-2 text-gray-400 hover:text-primary transition-colors">
                        <i data-lucide="eye" class="w-5 h-5"></i>
                    </a>
                </div>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $company->name }}</h3>
            <p class="text-sm text-gray-400 font-medium mb-6">{{ $company->industry ?? 'General Business' }}</p>
            
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100 mb-6">
                <div class="text-center">
                    <span class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1" x-text="lang === 'ar' ? 'المشاريع' : 'Projects'"></span>
                    <span class="text-lg font-bold text-gray-900">{{ $company->projects_count }}</span>
                </div>
                <div class="w-px h-8 bg-gray-200"></div>
                <div class="text-center">
                    <span class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1" x-text="lang === 'ar' ? 'الحالة' : 'Status'"></span>
                    <span class="text-[10px] font-black uppercase text-green-500 bg-green-50 px-2 py-0.5 rounded-md" x-text="lang === 'ar' ? 'نشط' : 'Active'"></span>
                </div>
            </div>

            <a href="{{ route('companies.show', $company->id) }}" class="w-full py-4 bg-gray-900 text-white rounded-2xl text-center text-sm font-bold hover:bg-black transition-all flex items-center justify-center gap-2">
                <span x-text="lang === 'ar' ? 'إدارة الشركة' : 'Manage Company'"></span>
                <i data-lucide="arrow-right" class="w-4 h-4 flip-rtl"></i>
            </a>
        </div>
        @empty
        <div class="col-span-full py-24 text-center bg-gray-50 rounded-[3rem] border-2 border-dashed border-gray-200">
            <div class="w-20 h-20 bg-white rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-sm">
                <i data-lucide="building" class="w-10 h-10 text-gray-300"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900" x-text="lang === 'ar' ? 'ابدأ بإضافة شركتك' : 'Start by adding your company'"></h3>
            <p class="text-gray-500 mt-2 max-w-sm mx-auto" x-text="lang === 'ar' ? 'تحتاج إلى ملف شركة لطلب الخدمات وإدارة مشاريعك.' : 'You need a company profile to request services and manage projects.'"></p>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Localization -->
<div id="add-company-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-[2.5rem] p-10 max-w-md w-full mx-4 shadow-2xl animate-in zoom-in duration-300">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-bold text-gray-900" x-text="lang === 'ar' ? 'إضافة شركة جديدة' : 'Add New Company'"></h3>
            <button onclick="document.getElementById('add-company-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-900"><i data-lucide="x" class="w-6 h-6"></i></button>
        </div>
        <form action="{{ route('companies.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="space-y-1">
                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400" x-text="lang === 'ar' ? 'اسم الشركة' : 'Company Name'"></label>
                <input type="text" name="name" required class="w-full px-5 py-4 border border-gray-100 bg-gray-50 rounded-2xl outline-none focus:ring-4 focus:ring-primary/10 transition-all font-medium">
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400" x-text="lang === 'ar' ? 'القطاع / الصناعة' : 'Industry'"></label>
                <select name="industry" class="w-full px-5 py-4 border border-gray-100 bg-gray-50 rounded-2xl outline-none focus:ring-4 focus:ring-primary/10 transition-all font-medium">
                    <option value="Technology" x-text="lang === 'ar' ? 'التقنية' : 'Technology'"></option>
                    <option value="Retail" x-text="lang === 'ar' ? 'التجزئة' : 'Retail'"></option>
                    <option value="Manufacturing" x-text="lang === 'ar' ? 'التصنيع' : 'Manufacturing'"></option>
                </select>
            </div>
            <button type="submit" class="w-full py-5 bg-primary text-white rounded-2xl font-bold text-lg hover:bg-primary-dark transition-all shadow-xl shadow-primary/20">
                <span x-text="lang === 'ar' ? 'إنشاء الملف' : 'Create Profile'"></span>
            </button>
        </form>
    </div>
</div>

<style>
    [dir="rtl"] .flip-rtl { transform: scaleX(-1); }
</style>
@endsection
