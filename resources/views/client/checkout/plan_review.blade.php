@extends('layouts.app')

@section('content')
<div class="max-w-4xl w-full mx-auto" 
     x-data="{ 
        billingCycle: '{{ $billingCycle }}',
        monthlyPrice: {{ (float)$plan->monthly_price }},
        annualPrice: {{ (float)$plan->annual_price }},
        annualMonthlyPrice: {{ (float)($plan->annual_price / 12) }},
        discount: {{ (int)$plan->annual_discount_percentage }},
        get currentAmount() {
            return this.billingCycle === 'annually' ? this.annualPrice : this.monthlyPrice;
        },
        get displayPrice() {
            const price = this.billingCycle === 'annually' ? this.annualMonthlyPrice : this.monthlyPrice;
            return price.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
     }">
    
    <div class="bg-white p-10 rounded-3xl shadow-sm border border-gray-100 flex flex-col md:flex-row gap-12">
        <!-- Left: Order Summary -->
        <div class="flex-1 space-y-8">
            <div>
                <h2 class="text-3xl font-normal text-gray-900 mb-2" x-text="t('common.review_plan_upgrade')"></h2>
                <p class="text-gray-500" x-text="t('common.confirm_plan_subscription')"></p>
            </div>

            <!-- Billing Cycle Toggle on Checkout -->
            <div class="flex items-center p-1 bg-gray-100 rounded-lg w-fit">
                <button type="button" @click="billingCycle = 'monthly'" 
                        :class="billingCycle === 'monthly' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500'" 
                        class="px-6 py-1.5 rounded-md text-xs font-normal transition-all">
                    {{ __('common.monthly') }}
                </button>
                <button type="button" @click="billingCycle = 'annually'" 
                        :class="billingCycle === 'annually' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500'" 
                        class="px-6 py-1.5 rounded-md text-xs font-normal transition-all">
                    {{ __('common.annually') }} 
                    <template x-if="discount > 0">
                        <span class="text-[10px] text-green-500 ml-1" x-text="'-' + discount + '%'"></span>
                    </template>
                </button>
            </div>

            <div class="bg-primary-light/50 p-6 rounded-3xl border border-primary/10 space-y-4">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-primary rounded-2xl flex items-center justify-center text-white">
                        <i data-lucide="layers" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="font-normal text-gray-900">{{ $plan->name }}</h3>
                        <p class="text-sm text-gray-500">{{ ucfirst($plan->type) }} {{ __('common.plan') }}</p>
                    </div>
                </div>
                
                <div class="pt-4 border-t border-primary/10 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500" x-text="t('common.subscription_fee')"></span>
                        <div class="text-right">
                            <span class="font-normal text-gray-900">
                                <span x-text="displayPrice"></span>
                                <span x-text="lang === 'ar' ? 'ر.س' : 'SAR'"></span>
                                <span class="text-[10px] text-gray-400">/ <span x-text="t('common.month')"></span></span>
                            </span>
                        </div>
                    </div>
                    <div class="flex justify-between text-lg pt-2 border-t border-primary/10">
                        <span class="font-normal text-gray-900" x-text="t('common.total')"></span>
                        <div class="text-right">
                            <span class="font-normal text-primary">
                                <span x-text="currentAmount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })"></span>
                                <span x-text="lang === 'ar' ? 'ر.س' : 'SAR'"></span>
                            </span>
                            <p class="text-[10px] text-gray-400" x-text="billingCycle === 'annually' ? 'Billed Annually' : 'Billed Monthly'"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <p class="text-xs font-normal uppercase tracking-widest text-gray-400" x-text="t('common.plan_features')"></p>
                <ul class="space-y-2">
                    <li class="flex items-center text-sm text-gray-600">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-green-500 mr-2"></i>
                        <span x-text="t('common.up_to') + ' ' + '{{ $plan->max_services }}' + ' ' + t('common.services')"></span>
                    </li>
                    <li class="flex items-center text-sm text-gray-600">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-green-500 mr-2"></i>
                        <span x-text="t('common.up_to') + ' ' + '{{ $plan->max_projects }}' + ' ' + t('common.projects')"></span>
                    </li>
                    <li class="flex items-center text-sm text-gray-600">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-green-500 mr-2"></i>
                        <span x-text="t('common.up_to') + ' ' + '{{ $plan->max_users }}' + ' ' + t('common.team_members')"></span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Right: Payment Method -->
        <div class="flex-1 space-y-6">
            <h3 class="text-xl font-normal text-gray-900" x-text="t('common.payment_method')"></h3>
            
            <form action="{{ route('checkout.plan.process') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                <input type="hidden" name="billing_cycle" x-model="billingCycle">
                
                @if(session('error'))
                    <div class="p-4 bg-red-50 text-red-600 rounded-2xl text-sm font-normal border border-red-100">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="space-y-4">
                    <div class="p-6 border-2 border-primary bg-primary-light rounded-2xl flex flex-col space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <i data-lucide="shield-check" class="w-6 h-6 text-primary"></i>
                                <span class="font-normal" x-text="t('common.secure_checkout')"></span>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($paymentSettings->tap_logo)
                                    <img src="{{ asset('storage/' . $paymentSettings->tap_logo) }}" alt="Tap" class="h-6">
                                @else
                                    <img src="/images/logo/tap-pay.png" alt="Tap" class="h-6">
                                @endif
                            </div>
                        </div>
                        <p class="text-sm text-gray-500" x-text="t('common.tap_redirect_notice')"></p>
                        <div class="flex space-x-2">
                            @if($paymentSettings->visa_logo)
                                <img src="{{ asset('storage/' . $paymentSettings->visa_logo) }}" alt="Visa" class="h-5">
                            @else
                                <img src="/images/logo/visa-card.png" alt="Visa" class="h-5">
                            @endif

                            @if($paymentSettings->mastercard_logo)
                                <img src="{{ asset('storage/' . $paymentSettings->mastercard_logo) }}" alt="Mastercard" class="h-5">
                            @else
                                <img src="/images/logo/master-card.png" alt="Mastercard" class="h-5">
                            @endif

                            @if($paymentSettings->mada_logo)
                                <img src="{{ asset('storage/' . $paymentSettings->mada_logo) }}" alt="Mada" class="h-5">
                            @else
                                <img src="/images/logo/mada-card.png" alt="Mada" class="h-5">
                            @endif
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full py-4 bg-primary text-white rounded-xl font-normal hover:bg-primary-dark transition-all shadow-xl shadow-primary/10 flex items-center justify-center space-x-2">
                    <span x-text="t('common.confirm_pay').replace(':amount', currentAmount.toLocaleString())"></span>
                    <i data-lucide="arrow-right" class="w-5 h-5 ml-2"></i>
                </button>
                <p class="text-center text-xs text-gray-400" x-text="t('common.secure_transaction_notice')"></p>
            </form>
        </div>
    </div>
</div>
@endsection
