@extends('layouts.app')

@section('content')
<div class="w-full h-[calc(100vh-120px)] max-w-7xl mx-auto flex flex-col space-y-6 animate-in fade-in duration-700">
    <!-- Header -->
    <div class="flex items-center justify-between bg-white p-4 rounded-lg border border-gray-100 shadow-sm">
        <div class="flex items-center space-x-4">
            @php
                $backRoute = Auth::user()->role === 'provider' 
                    ? route('provider.pre_sale_chats.index') 
                    : route('explore.show', $service->id);
            @endphp
            <a href="{{ $backRoute }}" class="w-10 h-10 bg-gray-50 border border-gray-100 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-900 transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5 flip-rtl"></i>
            </a>
            <div>
                <h1 class="text-xl font-normal text-gray-900 tracking-tight" x-text="t('common.consultation')"></h1>
                @if(Auth::user()->role === 'provider')
                    @php $client = \App\Models\User::find(request('client_id')); @endphp
                    <p class="text-[10px] font-normal text-gray-400 uppercase tracking-widest">{{ $service->name }} • {{ $client->name ?? 'Client' }}</p>
                @else
                    <p class="text-[10px] font-normal text-gray-400 uppercase tracking-widest">{{ $service->name }} • {{ $provider->providerProfile->company_name }}</p>
                @endif
            </div>
        </div>
        <div class="flex items-center space-x-3 bg-green-50 px-4 py-2 rounded-full border border-green-100">
            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
            <span class="text-[10px] font-normal uppercase text-green-600 tracking-widest" x-text="t('common.active')"></span>
        </div>
    </div>

    <!-- 2-Pane UI -->
    <div class="flex-1 flex gap-6 min-h-0">
        <!-- Left: Service Context (40%) -->
        <div class="w-1/3 bg-gray-50 rounded-lg p-8 border border-gray-100 overflow-y-auto custom-scrollbar flex flex-col">
            <div class="w-16 h-16 bg-white rounded-lg flex items-center justify-center text-primary shadow-lg shadow-primary/10 mb-6">
                <i data-lucide="{{ $service->icon }}" class="w-8 h-8"></i>
            </div>
            <h2 class="text-2xl font-normal text-gray-900 mb-3">{{ $service->name }}</h2>
            <p class="text-gray-500 text-sm leading-relaxed font-normal mb-8">{!! $service->description !!}</p>

            <div class="space-y-6 flex-1">
                <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm">
                    <!-- Participant Info -->
                    @if(Auth::user()->role === 'provider')
                        @php $client = \App\Models\User::find(request('client_id')); @endphp
                        <div class="flex items-center space-x-3 mb-6 pb-6 border-b border-gray-50">
                            <div class="w-10 h-10 bg-primary-light text-primary rounded-lg flex items-center justify-center font-normal text-xs uppercase">
                                {{ substr($client->name ?? 'C', 0, 2) }}
                            </div>
                            <div class="flex-1">
                                <p class="text-xs font-normal text-gray-900 leading-tight mb-1">{{ $client->name ?? 'Client' }}</p>
                                <p class="text-[10px] font-normal text-gray-400 uppercase tracking-tighter">Service Inquirer</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center space-x-3 mb-6 pb-6 border-b border-gray-50">
                            @if($provider->providerProfile->logo)
                                <img src="{{ asset('storage/' . $provider->providerProfile->logo) }}" alt="{{ $provider->providerProfile->company_name }}" class="w-10 h-10 rounded-lg object-cover">
                            @else
                                <div class="w-10 h-10 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center text-gray-300">
                                    <i data-lucide="building-2" class="w-5 h-5"></i>
                                </div>
                            @endif
                            <div class="flex-1">
                                <p class="text-xs font-normal text-gray-900 leading-tight mb-1">{{ $provider->providerProfile->company_name }}</p>
                                <div class="flex items-center space-x-1.5">
                                    <div class="flex items-center">
                                        <i data-lucide="star" class="w-3 h-3 text-yellow-400 fill-current"></i>
                                        <span class="text-[10px] font-normal text-gray-700 ms-1">{{ number_format($provider->average_rating, 1) }}</span>
                                    </div>
                                    <span class="text-[10px] text-gray-300">•</span>
                                    <span class="text-[10px] font-normal text-primary uppercase tracking-tighter" x-text="t('common.verified_provider')"></span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <p class="text-[10px] font-normal uppercase tracking-widest text-gray-400 mb-4">{{ __('common.standard_package') }}</p>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-normal text-gray-600" x-text="t('common.price')"></span>
                        <span class="text-lg font-normal text-primary">{{ number_format($ps->price, 0) }} <span x-text="t('common.sar')"></span></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-normal text-gray-600" x-text="t('common.delivery')"></span>
                        <span class="text-sm font-normal text-gray-900" x-text="t('common.days_count').replace(':count', '{{ $ps->delivery_time_days }}')"></span>
                    </div>
                </div>

                @if(Auth::user()->role === 'client')
                <div class="p-6 bg-primary-light rounded-lg border border-primary/10">
                    <p class="text-xs font-normal text-primary mb-2 flex items-center">
                        <i data-lucide="info" class="w-4 h-4 mr-2"></i>
                        Next Steps
                    </p>
                    <p class="text-[11px] text-primary/80 font-normal leading-relaxed">
                        After the consultation, you can proceed to request this service by clicking the button below. Funds will be held in escrow.
                    </p>
                </div>
                @endif
            </div>

            @if(Auth::user()->role === 'client')
            <div class="pt-8 mt-auto">
                <a href="{{ route('checkout.review', $ps->id) }}" class="w-full py-4 bg-primary text-white rounded-lg font-normal text-sm hover:bg-primary-dark transition-all shadow-lg shadow-primary/20 text-center flex items-center justify-center space-x-2">
                    <span x-text="t('common.request_service')"></span>
                    <i data-lucide="arrow-right" class="w-4 h-4 flip-rtl"></i>
                </a>
            </div>
            @endif
        </div>

        <!-- Right: Chat Window (60%) -->
        <div class="flex-1 bg-white rounded-lg border border-gray-100 shadow-sm flex flex-col overflow-hidden">
            <!-- Chat Messages -->
            <div class="flex-1 p-8 overflow-y-auto space-y-6 custom-scrollbar bg-white">
                <div class="text-center mb-8">
                    <span class="px-4 py-1 bg-gray-50 text-[10px] font-normal text-gray-400 uppercase tracking-widest rounded-full" x-text="t('common.secure_consultation')"></span>
                </div>

                @forelse($messages as $msg)
                <div class="flex items-start {{ $msg->sender_id === Auth::id() ? 'flex-row-reverse space-x-reverse' : 'space-x-3' }} max-w-[80%] {{ $msg->sender_id === Auth::id() ? 'ml-auto' : '' }}">
                    <div class="w-8 h-8 {{ $msg->sender_id === Auth::id() ? 'bg-gray-900 text-white' : 'bg-primary-light text-primary' }} rounded-lg flex items-center justify-center font-normal text-xs uppercase">
                        {{ substr($msg->sender->name, 0, 2) }}
                    </div>
                    <div class="{{ $msg->sender_id === Auth::id() ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'bg-gray-50 border border-gray-100 text-gray-700' }} p-4 rounded-lg {{ $msg->sender_id === Auth::id() ? 'rounded-tr-none' : 'rounded-tl-none' }}">
                        <p class="text-sm font-normal leading-relaxed">
                            {{ $msg->message }}
                        </p>
                        <span class="text-[8px] font-normal {{ $msg->sender_id === Auth::id() ? 'text-white/60' : 'text-gray-300' }} uppercase mt-2 block">{{ $msg->created_at->format('g:i A') }}</span>
                    </div>
                </div>
                @empty
                <div class="flex items-start space-x-3 max-w-[80%]">
                    <div class="w-8 h-8 bg-primary-light rounded-lg flex items-center justify-center text-primary font-normal text-xs uppercase">
                        {{ substr($provider->providerProfile->company_name, 0, 2) }}
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg rounded-tl-none border border-gray-100">
                        <p class="text-sm font-normal text-gray-700 leading-relaxed">
                            "{{ __('common.pre_sale_welcome', ['service' => $service->name]) }}"
                        </p>
                        <span class="text-[8px] font-normal text-gray-300 uppercase mt-2 block">{{ now()->format('g:i A') }}</span>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Chat Input -->
            <div class="p-6 bg-gray-50 border-t border-gray-100">
                <form action="{{ route('explore.chat.send', [$service->id, $provider->id]) }}" method="POST" class="flex flex-col space-y-4">
                    @csrf
                    @if(Auth::user()->role === 'provider')
                        <input type="hidden" name="client_id" value="{{ request('client_id') }}">
                    @endif

                    @if(Auth::user()->role === 'client' && $companies->count() > 0)
                    <div class="flex items-center space-x-2">
                        <span class="text-[10px] font-normal uppercase tracking-widest text-gray-400">Regarding:</span>
                        <select name="company_id" class="text-[10px] font-normal uppercase tracking-widest bg-transparent border-none focus:ring-0 text-primary cursor-pointer">
                            <option value="">General Inquiry</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ ($messages->where('company_id', $company->id)->count() > 0) ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="flex items-center space-x-4">
                        <div class="flex-1 relative">
                            <textarea name="message" required :placeholder="t('project.type_message')" rows="1" class="w-full pl-4 pr-12 py-4 bg-white border border-gray-200 rounded-lg focus:ring-4 focus:ring-primary/10 focus:border-primary/20 outline-none transition-all font-normal text-sm resize-none"></textarea>
                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-300 hover:text-primary transition-colors">
                                <i data-lucide="paperclip" class="w-5 h-5"></i>
                            </button>
                        </div>
                        <button type="submit" class="w-12 h-12 bg-primary text-white rounded-lg flex items-center justify-center hover:bg-primary-dark transition-all shadow-lg shadow-primary/20">
                            <i data-lucide="send" class="w-5 h-5"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
