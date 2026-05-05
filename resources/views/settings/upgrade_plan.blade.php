@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-black text-gray-900 mb-4">{{ __('common.upgrade_plan_title') }}</h1>
        <p class="text-lg text-gray-600">{{ __('common.upgrade_plan_subtitle') }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @foreach($plans as $plan)
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border-2 {{ Auth::user()->plan_id == $plan->id ? 'border-primary' : 'border-transparent' }} transition-all hover:scale-105">
            <div class="p-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-black text-gray-900">{{ __('common.' . strtolower($plan->name)) }}</h3>
                    @if(Auth::user()->plan_id == $plan->id)
                        <span class="px-2 py-1 bg-primary text-white text-[9px] font-black uppercase rounded-md">{{ __('common.current_plan') }}</span>
                    @endif
                </div>
                
                <div class="mb-8">
                    <span class="text-4xl font-black text-gray-900">{{ $plan->name === 'Enterprise' ? __('common.custom') : ($plan->name === 'Professional' ? '499' : '0') }}</span>
                    <span class="text-gray-500 font-medium">/{{ __('common.month') }}</span>
                </div>

                <ul class="space-y-4 mb-8">
                    <li class="flex items-start">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-3 flex-shrink-0"></i>
                        <span class="text-sm text-gray-600">
                            {{ $plan->max_services >= 999 ? __('common.unlimited_services') : __('common.up_to') . ' ' . $plan->max_services . ' ' . __('common.services') }}
                        </span>
                    </li>
                    <li class="flex items-start">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-3 flex-shrink-0"></i>
                        <span class="text-sm text-gray-600">
                            {{ $plan->max_projects >= 999 ? __('common.unlimited_projects') : __('common.up_to') . ' ' . $plan->max_projects . ' ' . __('common.projects') }}
                        </span>
                    </li>
                    <li class="flex items-start">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-3 flex-shrink-0"></i>
                        <span class="text-sm text-gray-600">
                            {{ $plan->max_users >= 999 ? __('common.unlimited_users') : __('common.up_to') . ' ' . $plan->max_users . ' ' . __('common.users') }}
                        </span>
                    </li>
                    @if($plan->type === 'client')
                    <li class="flex items-start">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-3 flex-shrink-0"></i>
                        <span class="text-sm text-gray-600">
                            {{ $plan->max_companies >= 999 ? __('common.unlimited_companies') : __('common.up_to') . ' ' . $plan->max_companies . ' ' . __('common.companies') }}
                        </span>
                    </li>
                    @endif
                </ul>

                @if(Auth::user()->plan_id != $plan->id)
                <form action="{{ route('settings.plan') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                    <button type="submit" class="w-full py-3 px-6 bg-primary text-white rounded-xl font-black uppercase tracking-widest text-xs hover:bg-primary-dark transition-all">
                        {{ __('common.select_plan') }}
                    </button>
                </form>
                @else
                <button disabled class="w-full py-3 px-6 bg-gray-100 text-gray-400 rounded-xl font-black uppercase tracking-widest text-xs cursor-not-allowed">
                    {{ __('common.active') }}
                </button>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
