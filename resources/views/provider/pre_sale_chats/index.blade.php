@extends('layouts.app')

@section('content')
<div class="w-full max-w-7xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <div>
        <h1 class="text-3xl font-normal text-gray-900 tracking-tight" x-text="t('common.pre_sale_chats')"></h1>
        <p class="text-gray-500 font-normal mt-1" x-text="t('common.no_pre_sale_chats_yet')"></p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($chats as $chat)
        <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-primary-light rounded-2xl flex items-center justify-center text-primary">
                        <i data-lucide="{{ $chat->service->icon }}" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="font-normal text-gray-900">{{ $chat->service->name }}</h4>
                        <p class="text-[10px] font-normal uppercase text-gray-400 tracking-widest">{{ $chat->client->name }}</p>
                    </div>
                </div>
                <div class="px-3 py-1 bg-amber-50 text-amber-600 rounded-full text-[10px] font-normal uppercase tracking-widest" x-text="t('common.consultation')"></div>
            </div>

            <div class="bg-gray-50 rounded-2xl p-4 mb-6">
                <p class="text-xs text-gray-500 font-normal italic line-clamp-2">"{{ $chat->message }}"</p>
                <p class="text-[9px] text-gray-300 font-normal uppercase mt-2">{{ $chat->created_at->diffForHumans() }}</p>
            </div>

            <a href="{{ route('explore.chat', ['serviceId' => $chat->service_id, 'providerId' => $chat->provider_id, 'client_id' => $chat->client_id]) }}" 
               class="w-full py-3 bg-gray-900 text-white rounded-xl font-normal text-sm hover:bg-black transition-all text-center flex items-center justify-center gap-2">
                <span x-text="t('common.view')"></span>
                <i data-lucide="arrow-right" class="w-4 h-4 flip-rtl"></i>
            </a>
        </div>
        @empty
        <div class="col-span-full py-20 text-center bg-gray-50 rounded-[2.5rem] border-2 border-dashed border-gray-100">
            <i data-lucide="message-circle" class="w-16 h-16 text-gray-200 mx-auto mb-4"></i>
            <h3 class="text-xl font-normal text-gray-900" x-text="t('common.no_pre_sale_chats_yet')"></h3>
        </div>
        @endforelse
    </div>
</div>
@endsection
