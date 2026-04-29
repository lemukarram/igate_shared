@extends('layouts.app')

@section('content')
<div class="max-w-7xl w-full space-y-8 animate-in fade-in duration-700" x-data="{ lang: localStorage.getItem('igate_lang') || 'en' }">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900" x-text="lang === 'ar' ? 'خدماتي وعروضي' : 'My Services & Portfolio'"></h1>
            <p class="text-gray-500 mt-1" x-text="lang === 'ar' ? 'إدارة الأسعار ونطاق التسليم لكل خدمة تقدمها.' : 'Manage pricing and delivery scope for each standardized service you provide.'"></p>
        </div>
        <button @click="addServiceOpen = true" class="px-6 py-3 bg-primary text-white rounded-xl font-bold hover:bg-primary-dark transition-all flex items-center gap-2 shadow-lg shadow-primary/20">
            <i data-lucide="plus" class="w-5 h-5"></i>
            <span x-text="lang === 'ar' ? 'إضافة خدمة جديدة' : 'Add New Service'"></span>
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- My Active Offerings -->
        <div class="lg:col-span-2 space-y-6">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-500"></i>
                <span x-text="lang === 'ar' ? 'الخدمات النشطة' : 'Active Offerings'"></span>
            </h2>
            
            <div class="grid grid-cols-1 gap-4">
                @forelse($myServices as $ps)
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-primary-light rounded-xl flex items-center justify-center text-primary">
                                <i data-lucide="{{ $ps->service->icon }}" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg">{{ $ps->service->name }}</h3>
                                <span class="text-[10px] font-black uppercase text-gray-400 tracking-widest">{{ $ps->service->category }}</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('explore.show', $ps->service->id) }}" class="p-2 text-gray-400 hover:text-primary transition-colors">
                                <i data-lucide="edit-3" class="w-5 h-5"></i>
                            </a>
                            <form action="{{ route('provider.services.destroy', $ps->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-500 transition-colors">
                                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <div>
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1" x-text="lang === 'ar' ? 'السعر' : 'Price'"></span>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($ps->price, 0) }} <span x-text="lang === 'ar' ? 'ر.س' : 'SAR'"></span></span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1" x-text="lang === 'ar' ? 'مدة التسليم' : 'Delivery'"></span>
                            <span class="text-sm font-bold text-gray-900">{{ $ps->delivery_time_days }} <span x-text="lang === 'ar' ? 'أيام' : 'Days'"></span></span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1" x-text="lang === 'ar' ? 'العملاء' : 'Clients'"></span>
                            <span class="text-sm font-bold text-gray-900">0</span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1" x-text="lang === 'ar' ? 'التقييم' : 'Rating'"></span>
                            <span class="flex items-center text-sm font-bold text-amber-500">
                                <i data-lucide="star" class="w-3 h-3 fill-current mr-1"></i> 5.0
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                    <p class="text-gray-500 font-medium" x-text="lang === 'ar' ? 'لم تضف أي خدمات بعد.' : 'No services added yet.'"></p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Catalog Sidebar -->
        <div class="space-y-6">
            <h2 class="text-lg font-bold text-gray-900" x-text="lang === 'ar' ? 'دليل الخدمات الموحد' : 'Standard Catalog'"></h2>
            <div class="bg-gray-900 rounded-3xl p-6 text-white shadow-xl shadow-gray-200">
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-4" x-text="lang === 'ar' ? 'لماذا الخدمات الموحدة؟' : 'Why Standardized?'"></p>
                <p class="text-sm text-gray-300 leading-relaxed mb-6" x-text="lang === 'ar' ? 'تضمن الخدمات الموحدة في آي غيت جودة تسليم ثابتة وتوقعات واضحة لكل من العميل والمزود.' : 'iGate standardized services ensure consistent delivery quality and clear expectations for both client and provider.'"></p>
                <ul class="space-y-4">
                    <li class="flex items-center gap-3 text-xs font-bold">
                        <i data-lucide="shield-check" class="w-4 h-4 text-primary"></i>
                        <span x-text="lang === 'ar' ? 'مدفوعات مضمونة' : 'Guaranteed Payments'"></span>
                    </li>
                    <li class="flex items-center gap-3 text-xs font-bold">
                        <i data-lucide="clock" class="w-4 h-4 text-primary"></i>
                        <span x-text="lang === 'ar' ? 'حماية اتفاقية الخدمة' : 'SLA Protection'"></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
