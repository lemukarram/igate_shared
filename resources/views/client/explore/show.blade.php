@extends('layouts.app')

@section('content')
<div class="max-w-6xl w-full space-y-8 p-4" x-data="{ lang: localStorage.getItem('igate_lang') || 'en' }">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('explore.index') }}" class="w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-900 transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5 flip-rtl"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $service->name }}</h1>
                <p class="text-gray-500">{{ $service->description }}</p>
            </div>
        </div>

        @if(auth()->user()->role === 'provider')
            <div class="flex items-center gap-3">
                @if($providerService)
                    <div class="bg-blue-50 px-4 py-2 rounded-xl border border-blue-100 flex items-center gap-3 me-2">
                        <div class="text-blue-600 font-bold text-xl">{{ $clientCount }}</div>
                        <div class="text-blue-500 text-xs uppercase font-bold leading-tight" x-text="lang === 'ar' ? 'العملاء<br>النشطون' : 'Active<br>Clients'"></div>
                    </div>
                    <form action="{{ route('provider.services.destroy', $providerService->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-6 py-3 bg-red-50 text-red-600 rounded-xl font-bold hover:bg-red-100 transition-all border border-red-100">
                            <span x-text="lang === 'ar' ? 'حذف الخدمة' : 'Remove Service'"></span>
                        </button>
                    </form>
                @else
                    <button onclick="document.getElementById('add-service-modal').classList.remove('hidden')" class="px-6 py-3 bg-primary text-white rounded-xl font-bold hover:bg-primary-dark transition-all shadow-sm">
                        <span x-text="lang === 'ar' ? 'إضافة إلى خدماتي' : 'Add to My Services'"></span>
                    </button>
                @endif
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-sm">
                <h2 class="text-xl font-bold text-gray-900 mb-6" x-text="lang === 'ar' ? 'نطاق الخدمة والمهام الفرعية' : 'Service Scope & Subtasks'"></h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($service->subtasks)
                        @foreach($service->subtasks as $subtask)
                            <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <div class="mt-1 w-5 h-5 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="check" class="w-3 h-3 text-blue-600"></i>
                                </div>
                                <span class="text-gray-700 font-medium">{{ $subtask }}</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-500 italic" x-text="lang === 'ar' ? 'لم يتم تحديد مهام فرعية لهذه الخدمة.' : 'No subtasks defined for this service.'"></p>
                    @endif
                </div>
            </div>

            @if(auth()->user()->role === 'client')
            <div class="space-y-6">
                <h2 class="text-xl font-bold text-gray-900" x-text="lang === 'ar' ? 'المزودون المتاحون' : 'Available Providers'"></h2>
                
                <div class="grid grid-cols-1 gap-4">
                    @forelse($providers as $ps)
                    <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all flex flex-col md:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-4 flex-1">
                            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center overflow-hidden">
                                @if($ps->provider->providerProfile && $ps->provider->providerProfile->logo)
                                    <img src="{{ asset('storage/' . $ps->provider->providerProfile->logo) }}" class="w-full h-full object-cover">
                                @else
                                    <i data-lucide="building-2" class="w-8 h-8 text-gray-400"></i>
                                @endif
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $ps->provider->providerProfile->company_name ?? $ps->provider->name }}</h3>
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <span class="flex items-center"><i data-lucide="star" class="w-4 h-4 text-yellow-400 fill-current me-1"></i> 5.0</span>
                                    <span>•</span>
                                    <span x-text="lang === 'ar' ? 'مزود موثق' : 'Verified Provider'"></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-12 px-8 border-x border-gray-50 hidden lg:flex">
                            <div class="text-center">
                                <span class="block text-xs text-gray-400 uppercase font-semibold" x-text="lang === 'ar' ? 'التسليم' : 'Delivery'"></span>
                                <span class="text-sm font-bold text-gray-900">{{ $ps->delivery_time_days }} <span x-text="lang === 'ar' ? 'أيام' : 'Days'"></span></span>
                            </div>
                            <div class="text-center">
                                <span class="block text-xs text-gray-400 uppercase font-semibold" x-text="lang === 'ar' ? 'السعر' : 'Price'"></span>
                                <span class="text-xl font-black text-blue-600">{{ number_format($ps->price, 0) }} <span x-text="lang === 'ar' ? 'ر.س' : 'SAR'"></span></span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 w-full md:w-auto">
                            <a href="{{ route('explore.chat', ['serviceId' => $service->id, 'providerId' => $ps->provider_id]) }}" 
                               class="flex-1 md:flex-none px-6 py-3 bg-gray-50 text-gray-700 rounded-xl font-bold hover:bg-gray-100 transition-all border border-gray-100 text-center">
                                <span x-text="lang === 'ar' ? 'محادثة' : 'Chat'"></span>
                            </a>
                            <a href="{{ route('checkout.review', $ps->id) }}" class="flex-1 md:flex-none px-8 py-3 bg-primary text-white rounded-lg font-bold hover:bg-primary-dark transition-all shadow-sm text-center">
                                <span x-text="lang === 'ar' ? 'طلب الخدمة' : 'Request'"></span>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="py-20 text-center bg-gray-50 rounded-3xl border border-dashed border-gray-200">
                        <i data-lucide="users-2" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-900" x-text="lang === 'ar' ? 'لا يوجد مزودون حالياً' : 'No providers yet'"></h3>
                        <p class="text-gray-500" x-text="lang === 'ar' ? 'نحن نقوم حالياً بضم خبراء لهذه الخدمة.' : 'We are currently onboarding experts for this service.'"></p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            @if(auth()->user()->role === 'provider' && $providerService)
            <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900" x-text="lang === 'ar' ? 'تفاصيل عرضي' : 'My Offering Details'"></h2>
                    <button onclick="document.getElementById('edit-service-modal').classList.remove('hidden')" class="px-4 py-2 bg-gray-900 text-white rounded-xl text-sm font-bold hover:bg-gray-800 transition-all flex items-center gap-2">
                        <i data-lucide="edit-3" class="w-4 h-4"></i> <span x-text="lang === 'ar' ? 'تعديل التفاصيل' : 'Edit Details'"></span>
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div class="p-6 bg-blue-50 rounded-2xl border border-blue-100">
                        <span class="block text-xs text-blue-500 uppercase font-black tracking-widest mb-1" x-text="lang === 'ar' ? 'سعري' : 'My Price'"></span>
                        <span class="text-3xl font-black text-blue-700">{{ number_format($providerService->price, 0) }} <span class="text-sm" x-text="lang === 'ar' ? 'ر.س' : 'SAR'"></span></span>
                    </div>
                    <div class="p-6 bg-indigo-50 rounded-2xl border border-indigo-100">
                        <span class="block text-xs text-indigo-500 uppercase font-black tracking-widest mb-1" x-text="lang === 'ar' ? 'مدة التسليم' : 'Delivery Time'"></span>
                        <span class="text-3xl font-black text-indigo-700">{{ $providerService->delivery_time_days }} <span class="text-sm" x-text="lang === 'ar' ? 'أيام' : 'Days'"></span></span>
                    </div>
                </div>

                <div>
                    <span class="block text-xs text-gray-400 uppercase font-black tracking-widest mb-3" x-text="lang === 'ar' ? 'ملاحظات الخدمة / الشروط' : 'Service Notes / Terms'"></span>
                    <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100 text-gray-700 leading-relaxed italic">
                        {{ $providerService->provider_notes ?? (Auth::user()->role === 'provider' ? 'لا توجد ملاحظات محددة مضافة حالياً.' : 'No specific notes added yet.') }}
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-blue-600 rounded-3xl p-8 text-white">
                <h3 class="text-xl font-bold mb-4" x-text="lang === 'ar' ? 'معايير آي غيت' : 'iGate Standard'"></h3>
                <p class="text-blue-100 mb-6 text-sm leading-relaxed" x-text="lang === 'ar' ? 'تتبع جميع الخدمات في آي غيت إرشادات جودة صارمة ونطاقات تسليم موحدة.' : 'All services on iGate follow strict quality guidelines and standardized delivery scopes.'"></p>
                <ul class="space-y-4">
                    <li class="flex items-center gap-3 text-sm font-bold">
                        <i data-lucide="shield-check" class="w-5 h-5 text-blue-300"></i>
                        <span x-text="lang === 'ar' ? 'مدفوعات آمنة عبر الحساب الوسيط' : 'Secure Escrow Payments'"></span>
                    </li>
                    <li class="flex items-center gap-3 text-sm font-bold">
                        <i data-lucide="clock" class="w-5 h-5 text-blue-300"></i>
                        <span x-text="lang === 'ar' ? 'تسليم مضمون حسب اتفاقية الخدمة' : 'SLA Guaranteed Delivery'"></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
    [dir="rtl"] .flip-rtl { transform: scaleX(-1); }
</style>
@endsection
