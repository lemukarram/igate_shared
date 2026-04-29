@extends('layouts.app')

@section('content')
<div class="max-w-7xl w-full space-y-8 animate-in fade-in duration-700" x-data="{ lang: localStorage.getItem('igate_lang') || 'en' }">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900" x-text="lang === 'ar' ? 'قائمة العملاء' : 'My Clients'"></h1>
            <p class="text-gray-500 mt-1" x-text="lang === 'ar' ? 'إدارة العملاء والمشاريع النشطة.' : 'Manage your active client relationships and projects.'"></p>
        </div>
        <div class="flex gap-3">
            <button class="px-5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 transition-all flex items-center gap-2">
                <i data-lucide="filter" class="w-4 h-4 text-gray-400"></i>
                <span x-text="lang === 'ar' ? 'تصفية' : 'Filter'"></span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($clients as $clientId => $clientProjects)
            @php $client = $clientProjects->first()->client; @endphp
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all flex flex-col">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 bg-primary-light rounded-xl flex items-center justify-center text-primary font-black text-xl">
                        {{ substr($client->name, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $client->name }}</h3>
                        <p class="text-xs text-gray-400 font-medium">{{ $client->email }}</p>
                    </div>
                </div>

                <div class="space-y-3 flex-1 mb-6">
                    <div class="flex justify-between text-xs font-bold text-gray-400 uppercase tracking-widest">
                        <span x-text="lang === 'ar' ? 'المشاريع النشطة' : 'Active Projects'"></span>
                        <span class="text-gray-900">{{ $clientProjects->count() }}</span>
                    </div>
                    <div class="space-y-2">
                        @foreach($clientProjects as $project)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                            <span class="text-sm font-medium text-gray-700">{{ $project->service->name }}</span>
                            <span class="text-[10px] font-black uppercase text-green-500 bg-green-50 px-2 py-0.5 rounded-md" x-text="lang === 'ar' ? 'نشط' : 'Active'"></span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('provider.clients.show', $client->id) }}" class="w-full py-3 bg-gray-900 text-white rounded-xl text-center text-sm font-bold hover:bg-black transition-all">
                    <span x-text="lang === 'ar' ? 'عرض ملف العميل' : 'View Client Profile'"></span>
                </a>
            </div>
        @empty
            <div class="col-span-full py-20 text-center bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
                <i data-lucide="users" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
                <h3 class="text-xl font-bold text-gray-900" x-text="lang === 'ar' ? 'لا يوجد عملاء بعد' : 'No clients yet'"></h3>
                <p class="text-gray-500 mt-2" x-text="lang === 'ar' ? 'بمجرد أن يطلب العملاء خدماتك، سيظهرون هنا.' : 'Once clients request your services, they will appear here.'"></p>
            </div>
        @endforelse
    </div>
</div>
@endsection
