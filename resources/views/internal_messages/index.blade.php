@extends('layouts.app')

@section('content')
<div class="w-full max-w-5xl mx-auto h-[calc(100vh-8rem)] flex flex-col animate-in fade-in duration-700 bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
    
    <!-- Top Header -->
    <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-primary-light rounded-lg flex items-center justify-center text-primary">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold text-gray-900" x-text="lang === 'ar' ? 'التواصل الداخلي' : 'Internal Communication'"></h1>
                <p class="text-xs text-gray-500 font-medium">{{ $team->name }}</p>
            </div>
        </div>
    </div>

    <!-- Chat Workspace -->
    <div class="flex-1 p-6 overflow-y-auto space-y-6 custom-scrollbar bg-white" id="chat-messages">
        @forelse($messages as $msg)
        <div class="flex gap-4 {{ $msg->user_id === Auth::id() ? 'flex-row-reverse' : '' }}">
            <div class="w-8 h-8 rounded-full {{ $msg->user_id === Auth::id() ? 'bg-primary' : 'bg-gray-900' }} flex-shrink-0 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                {{ substr($msg->user->name, 0, 1) }}
            </div>
            <div class="flex flex-col {{ $msg->user_id === Auth::id() ? 'items-end' : 'items-start' }} max-w-[70%]">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs font-bold text-gray-900">
                        {{ $msg->user_id === Auth::id() ? (app()->getLocale() == 'ar' ? 'أنت' : 'You') : $msg->user->name }}
                    </span>
                    @php
                        $teamRole = $msg->user_id === $team->owner_id ? 'Owner' : ($msg->user->teamMemberships()->where('team_id', $team->id)->first()->role ?? 'Staff');
                    @endphp
                    <span class="text-[9px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-500 uppercase tracking-widest font-black">{{ ucfirst($teamRole) }}</span>
                    <span class="text-[10px] text-gray-400 ml-1">{{ $msg->created_at->format('M d, g:i A') }}</span>
                </div>
                <div class="text-sm leading-relaxed p-4 rounded-2xl border {{ $msg->user_id === Auth::id() ? 'bg-primary text-white border-primary rounded-tr-sm' : 'bg-gray-50 text-gray-800 border-gray-100 rounded-tl-sm' }}">
                    <p>{{ $msg->message }}</p>
                </div>
            </div>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center h-full text-gray-400 space-y-4">
            <i data-lucide="message-square" class="w-12 h-12 opacity-50"></i>
            <p class="text-sm font-medium" x-text="lang === 'ar' ? 'ابدأ المحادثة مع فريقك.' : 'Start the conversation with your team.'"></p>
        </div>
        @endforelse
    </div>

    <!-- Chat Input -->
    <div class="p-4 bg-white border-t border-gray-100">
        <form action="{{ route('internal-messages.store') }}" method="POST">
            @csrf
            <input type="hidden" name="team_id" value="{{ $team->id }}">
            <div class="relative bg-gray-50 border border-gray-200 rounded-2xl shadow-sm focus-within:ring-2 focus-within:ring-primary/50 focus-within:border-primary transition-all">
                <textarea name="message" required :placeholder="lang === 'ar' ? 'اكتب رسالتك هنا...' : 'Type your message here...'" rows="2" class="w-full ps-4 pe-16 py-3 bg-transparent outline-none font-medium text-sm text-gray-700 resize-none custom-scrollbar"></textarea>
                <div class="absolute inset-y-2 end-2 flex items-center gap-1">
                    <button type="submit" class="p-2 bg-primary text-white rounded-xl flex items-center justify-center hover:bg-primary-dark transition-colors shadow-md">
                        <i data-lucide="send" class="w-4 h-4 rtl-flip"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var container = document.getElementById("chat-messages");
        container.scrollTop = container.scrollHeight;
    });
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    [dir="rtl"] .rtl-flip { transform: scaleX(-1); }
</style>
@endsection
