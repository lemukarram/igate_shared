<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('common.landing_title') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400&display=swap');
        body { font-family: 'Poppins', sans-serif; font-weight: 300; }
        h1, h2, h3, h4, .font-normal { font-weight: 400 !important; }
        
        .theme-bg { background-color: #3da9e4; }
        .theme-text { color: #3da9e4; }
        .theme-border { border-color: #3da9e4; }
        .theme-hover-bg:hover { background-color: #2b8bc2; }
        
        .hero-gradient { background: linear-gradient(135deg, #f0f7ff 0%, #ffffff 100%); }
        .animate-float { animation: float 6s ease-in-out infinite; }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
        }
        
        .fancy-border {
            position: relative;
        }
        .fancy-border::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: #3da9e4;
            border-radius: 0.5rem;
            transition: width 0.3s ease;
        }
        .fancy-border:hover::after {
            width: 100%;
        }
        [dir="rtl"] .fancy-border::after {
            left: auto;
            right: 0;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden selection:bg-[#3da9e4] selection:text-white">
    <!-- Navbar -->
    <nav class="fixed w-full z-50 glass-card">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center">
                <img src="/images/logo/logo.png" alt="iGate Shared Services" class="h-10 object-contain">
            </div>
            
            <div class="hidden lg:flex items-center space-x-10 text-sm font-normal text-gray-600 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }}">
                <a href="#about" class="hover:theme-text transition-all fancy-border">{{ __('common.about') }}</a>
                <a href="#services" class="hover:theme-text transition-all fancy-border">{{ __('common.services') }}</a>
                <a href="#why-us" class="hover:theme-text transition-all fancy-border">{{ __('common.why_igate') }}</a>
                <a href="/terms" class="hover:theme-text transition-all fancy-border">{{ __('common.terms') }}</a>
            </div>

            <div class="flex items-center space-x-4 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }}">
                <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-lg mr-2">
                    <button onclick="setLang('ar')" class="{{ app()->getLocale() === 'ar' ? 'bg-[#3da9e4] text-white' : 'text-gray-400 hover:text-gray-600' }} px-3 py-1 text-[10px] font-normal uppercase transition-all rounded-md">ar</button>
                    <button onclick="setLang('en')" class="{{ app()->getLocale() === 'en' ? 'bg-[#3da9e4] text-white' : 'text-gray-400 hover:text-gray-600' }} px-3 py-1 text-[10px] font-normal uppercase transition-all rounded-md">en</button>
                </div>
                <a href="/login" class="text-sm font-normal text-gray-700 hover:theme-text transition-colors">{{ __('common.signin') }}</a>
                <a href="/login" class="theme-bg text-white px-6 py-2.5 rounded-lg font-normal text-sm theme-hover-bg transition-all shadow-md active:scale-95">{{ __('common.get_started') }}</a>
            </div>
        </div>
    </nav>

    <script>
        lucide.createIcons();
        function setLang(newLang) {
            const currentLang = "{{ app()->getLocale() }}";
            if (currentLang === newLang) return;
            localStorage.setItem('igate_lang', newLang);
            document.cookie = "igate_lang=" + newLang + ";path=/;max-age=" + (365 * 24 * 60 * 60);
            location.reload();
        }
    </script>

    <!-- Hero Section -->
    <section class="relative pt-40 pb-20 px-6 hero-gradient overflow-hidden">
        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center justify-between relative z-10 gap-12">
            <div class="w-full lg:w-1/2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                <div class="inline-flex items-center space-x-2 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }} bg-white border border-[#3da9e4] theme-text px-4 py-1.5 rounded-md text-xs font-normal uppercase tracking-wider mb-6 shadow-sm">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#3da9e4] opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-[#3da9e4]"></span>
                    </span>
                    <span>{{ __('common.operating_system_ksa') }}</span>
                </div>
                
                <h1 class="text-5xl lg:text-6xl font-normal text-gray-900 leading-tight mb-6">
                    {!! str_replace('Absolute Trust.', '<span class="theme-text">' . __('common.absolute_trust') . '</span>', __('common.hero_title')) !!}
                    @if(app()->getLocale() == 'ar')
                        <span class="theme-text">{{ __('common.absolute_trust') }}</span>
                    @endif
                </h1>
                
                <p class="text-lg text-gray-600 leading-relaxed mb-10 font-normal">
                    {{ __('common.hero_subtitle') }}
                </p>
                
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <a href="/login" class="w-full sm:w-auto px-8 py-3.5 theme-bg text-white rounded-lg font-normal hover:bg-[#2b8bc2] transition-all shadow-lg flex items-center justify-center space-x-2 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }}">
                        <span>{{ __('common.join_as_client') }}</span>
                        <i data-lucide="{{ app()->getLocale() == 'ar' ? 'arrow-left' : 'arrow-right' }}" class="w-5 h-5"></i>
                    </a>
                    <a href="/login" class="w-full sm:w-auto px-8 py-3.5 bg-white text-gray-800 border border-gray-200 rounded-lg font-normal hover:bg-gray-50 transition-all flex items-center justify-center space-x-2 shadow-sm">
                        <span>{{ __('common.join_as_provider') }}</span>
                    </a>
                </div>
            </div>

            <!-- Dashboard Preview Graphic -->
            <div class="w-full lg:w-1/2 relative animate-float">
                <div class="glass-card rounded-lg p-3 shadow-xl relative z-10 border-t border-l border-white/60">
                    <img src="/images/igate-banner.jpg" class="w-full rounded-md " alt="Dashboard Preview" style="min-height: 300px; object-fit: contain; background: #fafafa;">
                    
                    <div class="absolute {{ app()->getLocale() == 'ar' ? '-right-6' : '-left-6' }} top-10 bg-white p-4 rounded-lg shadow-lg border border-gray-100 flex items-center space-x-3 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }}">
                        <div class="w-10 h-10 bg-[#e6f4fd] rounded-md flex items-center justify-center theme-text">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-normal uppercase tracking-wide">{{ __('common.payment_released') }}</p>
                            <p class="text-sm font-normal">24,500 {{ __('common.sar') }}</p>
                        </div>
                    </div>

                    <div class="absolute {{ app()->getLocale() == 'ar' ? '-left-6' : '-right-6' }} bottom-10 bg-white p-4 rounded-lg shadow-lg border border-gray-100 flex items-center space-x-3 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }}">
                        <div class="w-10 h-10 bg-[#e6f4fd] rounded-md flex items-center justify-center theme-text">
                            <i data-lucide="shield-check" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-normal uppercase tracking-wide">{{ __('common.sla_verified') }}</p>
                            <p class="text-sm font-normal">{{ __('common.success_rate') }}</p>
                        </div>
                    </div>
                </div>
                <!-- decorative blobs -->
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-72 h-72 bg-[#3da9e4] rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
                <div class="absolute top-1/2 left-1/4 -translate-x-1/2 -translate-y-1/2 w-72 h-72 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
            </div>
        </div>
    </section>

    <!-- Why iGate Shared Services Section -->
    <section id="why-us" class="py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-normal text-gray-900 mb-4">{{ __('common.why_igate_title') }}</h2>
                <p class="text-gray-500 font-normal max-w-2xl mx-auto">{{ __('common.why_igate_subtitle') }}</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Card 1 -->
                <div class="p-8 rounded-lg border border-gray-100 bg-gray-50 hover:bg-white hover:shadow-xl transition-all duration-300 group cursor-pointer {{ app()->getLocale() == 'ar' ? 'text-right' : '' }}">
                    <div class="w-14 h-14 bg-[#e6f4fd] rounded-lg flex items-center justify-center theme-text mb-6 group-hover:bg-[#3da9e4] group-hover:text-white transition-colors mx-auto lg:mx-0">
                        <i data-lucide="lock" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-xl font-normal mb-3 text-gray-800">{{ __('common.escrow_security') }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed font-normal">{{ __('common.escrow_description') }}</p>
                </div>
                <!-- Card 2 -->
                <div class="p-8 rounded-lg border border-gray-100 bg-gray-50 hover:bg-white hover:shadow-xl transition-all duration-300 group cursor-pointer mt-0 lg:mt-8 {{ app()->getLocale() == 'ar' ? 'text-right' : '' }}">
                    <div class="w-14 h-14 bg-[#e6f4fd] rounded-lg flex items-center justify-center theme-text mb-6 group-hover:bg-[#3da9e4] group-hover:text-white transition-colors mx-auto lg:mx-0">
                        <i data-lucide="layout-grid" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-xl font-normal mb-3 text-gray-800">{{ __('common.fixed_scopes') }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed font-normal">{{ __('common.fixed_scopes_description') }}</p>
                </div>
                <!-- Card 3 -->
                <div class="p-8 rounded-lg border border-gray-100 bg-gray-50 hover:bg-white hover:shadow-xl transition-all duration-300 group cursor-pointer {{ app()->getLocale() == 'ar' ? 'text-right' : '' }}">
                    <div class="w-14 h-14 bg-[#e6f4fd] rounded-lg flex items-center justify-center theme-text mb-6 group-hover:bg-[#3da9e4] group-hover:text-white transition-colors mx-auto lg:mx-0">
                        <i data-lucide="check-square" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-xl font-normal mb-3 text-gray-800">{{ __('common.verified_providers') }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed font-normal">{{ __('common.verified_providers_description') }}</p>
                </div>
                <!-- Card 4 -->
                <div class="p-8 rounded-lg border border-gray-100 bg-gray-50 hover:bg-white hover:shadow-xl transition-all duration-300 group cursor-pointer mt-0 lg:mt-8 {{ app()->getLocale() == 'ar' ? 'text-right' : '' }}">
                    <div class="w-14 h-14 bg-[#e6f4fd] rounded-lg flex items-center justify-center theme-text mb-6 group-hover:bg-[#3da9e4] group-hover:text-white transition-colors mx-auto lg:mx-0">
                        <i data-lucide="activity" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-xl font-normal mb-3 text-gray-800">{{ __('common.sla_tracking') }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed font-normal">{{ __('common.sla_tracking_description') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Grid -->
    <section id="services" class="py-24 bg-gray-50 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12 border-b border-gray-200 pb-6">
                <div class="max-w-2xl {{ app()->getLocale() == 'ar' ? 'text-right' : '' }}">
                    <h2 class="text-3xl font-normal text-gray-900 mb-3">{{ __('common.core_services_catalog') }}</h2>
                    <p class="text-gray-500 font-normal">{{ __('common.core_services_subtitle') }}</p>
                </div>
                <a href="/login" class="theme-text font-normal flex items-center space-x-2 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }} hover:text-[#2b8bc2] transition-colors mt-4 md:mt-0">
                    <span>{{ __('common.view_all_services') }}</span>
                    <i data-lucide="{{ app()->getLocale() == 'ar' ? 'arrow-left' : 'arrow-right' }}" class="w-4 h-4"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-lg border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group {{ app()->getLocale() == 'ar' ? 'text-right' : '' }}">
                    <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0 rounded-br-full -ml-12' : 'right-0 rounded-bl-full -mr-12' }} w-24 h-24 bg-[#e6f4fd] -mt-12 transition-transform group-hover:scale-110"></div>
                    <div class="w-12 h-12 bg-[#e6f4fd] rounded-md flex items-center justify-center theme-text mb-6 relative z-10 group-hover:bg-[#3da9e4] group-hover:text-white transition-colors {{ app()->getLocale() == 'ar' ? 'mr-0 ml-auto' : '' }}">
                        <i data-lucide="landmark" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-xl font-normal mb-3 text-gray-800">{{ __('common.zatca_vat') }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed font-normal mb-6">{{ __('common.zatca_vat_description') }}</p>
                    <a href="/login" class="text-sm theme-text font-normal flex items-center space-x-1 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }} group-hover:underline">
                        <span>{{ __('common.learn_more') }}</span>
                        <i data-lucide="{{ app()->getLocale() == 'ar' ? 'chevron-left' : 'chevron-right' }}" class="w-4 h-4"></i>
                    </a>
                </div>
                <div class="bg-white p-8 rounded-lg border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group {{ app()->getLocale() == 'ar' ? 'text-right' : '' }}">
                    <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0 rounded-br-full -ml-12' : 'right-0 rounded-bl-full -mr-12' }} w-24 h-24 bg-[#e6f4fd] -mt-12 transition-transform group-hover:scale-110"></div>
                    <div class="w-12 h-12 bg-[#e6f4fd] rounded-md flex items-center justify-center theme-text mb-6 relative z-10 group-hover:bg-[#3da9e4] group-hover:text-white transition-colors {{ app()->getLocale() == 'ar' ? 'mr-0 ml-auto' : '' }}">
                        <i data-lucide="users" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-xl font-normal mb-3 text-gray-800">{{ __('common.human_capital') }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed font-normal mb-6">{{ __('common.human_capital_description') }}</p>
                    <a href="/login" class="text-sm theme-text font-normal flex items-center space-x-1 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }} group-hover:underline">
                        <span>{{ __('common.learn_more') }}</span>
                        <i data-lucide="{{ app()->getLocale() == 'ar' ? 'chevron-left' : 'chevron-right' }}" class="w-4 h-4"></i>
                    </a>
                </div>
                <div class="bg-white p-8 rounded-lg border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group {{ app()->getLocale() == 'ar' ? 'text-right' : '' }}">
                    <div class="absolute top-0 {{ app()->getLocale() == 'ar' ? 'left-0 rounded-br-full -ml-12' : 'right-0 rounded-bl-full -mr-12' }} w-24 h-24 bg-[#e6f4fd] -mt-12 transition-transform group-hover:scale-110"></div>
                    <div class="w-12 h-12 bg-[#e6f4fd] rounded-md flex items-center justify-center theme-text mb-6 relative z-10 group-hover:bg-[#3da9e4] group-hover:text-white transition-colors {{ app()->getLocale() == 'ar' ? 'mr-0 ml-auto' : '' }}">
                        <i data-lucide="scale" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-xl font-normal mb-3 text-gray-800">{{ __('common.legal_advisory') }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed font-normal mb-6">{{ __('common.legal_advisory_description') }}</p>
                    <a href="/login" class="text-sm theme-text font-normal flex items-center space-x-1 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }} group-hover:underline">
                        <span>{{ __('common.learn_more') }}</span>
                        <i data-lucide="{{ app()->getLocale() == 'ar' ? 'chevron-left' : 'chevron-right' }}" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Subscription Plans -->
    <section id="pricing" class="py-24 bg-white px-6" x-data="{ role: 'client', billing: 'monthly' }">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-normal text-gray-900 mb-4">{{ __('common.subscription_plans') }}</h2>
                <p class="text-gray-500 font-normal max-w-2xl mx-auto">{{ __('common.subscription_subtitle') }}</p>
                
                <div class="mt-10 flex flex-col items-center gap-6">
                    <!-- Role Switcher -->
                    <div class="flex items-center p-1 bg-gray-100 rounded-lg w-fit">
                        <button @click="role = 'client'" :class="role === 'client' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500'" class="px-6 py-2 rounded-md text-sm font-normal transition-all">{{ __('common.client') }}</button>
                        <button @click="role = 'provider'" :class="role === 'provider' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500'" class="px-6 py-2 rounded-md text-sm font-normal transition-all">{{ __('common.service_provider') }}</button>
                    </div>

                    <!-- Billing Toggle -->
                    <div class="flex items-center gap-3">
                        <span :class="billing === 'monthly' ? 'text-gray-900' : 'text-gray-400'" class="text-sm font-normal transition-colors">{{ __('common.monthly') }}</span>
                        <button @click="billing = billing === 'monthly' ? 'annual' : 'monthly'" class="w-12 h-6 bg-gray-200 rounded-full relative transition-colors focus:outline-none" :class="billing === 'annual' ? 'bg-[#3da9e4]' : 'bg-gray-200'">
                            <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform" :class="billing === 'annual' ? 'translate-x-6' : ''"></div>
                        </button>
                        <span :class="billing === 'annual' ? 'text-gray-900' : 'text-gray-400'" class="text-sm font-normal transition-colors">{{ __('common.annually') }}</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @php
                    $plans = \App\Models\Plan::all();
                    $clientPlans = $plans->where('type', 'client')->values();
                    $providerPlans = $plans->where('type', 'provider')->values();
                @endphp

                <!-- Client Plans -->
                <div class="contents" x-show="role === 'client'">
                    @foreach($clientPlans as $index => $plan)
                    <div class="p-8 rounded-lg border {{ $index === 1 ? 'border-[#3da9e4] ring-1 ring-[#3da9e4]' : 'border-gray-100' }} bg-white flex flex-col {{ app()->getLocale() == 'ar' ? 'text-right' : '' }} relative">
                        @if($index === 1)
                            <span class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-[#3da9e4] text-white px-3 py-1 rounded-full text-[10px] font-normal uppercase tracking-wider">{{ __('common.most_popular') }}</span>
                        @endif
                        <h3 class="text-xl font-normal text-gray-900 mb-2">{{ $plan->name }}</h3>
                        <p class="text-sm text-gray-500 font-normal mb-6">{{ $plan->description ?? 'Ideal for individuals and small teams.' }}</p>
                        
                        <div class="mb-8">
                            <span class="text-4xl font-normal text-gray-900" x-text="billing === 'monthly' ? '{{ number_format($plan->price) }}' : '{{ number_format($plan->price * 10) }}'"></span>
                            <span class="text-gray-400 font-normal">{{ __('common.sar') }}</span>
                            <span class="text-gray-400 text-sm font-normal">/ <span x-text="billing === 'monthly' ? '{{ __('common.mo') }}' : '{{ __('common.yr') }}'"></span></span>
                        </div>

                        <ul class="space-y-4 mb-10 flex-1">
                            <li class="flex items-center gap-3 text-sm text-gray-600 font-normal {{ app()->getLocale() == 'ar' ? 'flex-row-reverse' : '' }}">
                                <i data-lucide="check" class="w-4 h-4 text-green-500 flex-shrink-0"></i>
                                <span>{{ $plan->max_services == 999 ? __('common.unlimited_services') : $plan->max_services . ' ' . __('common.services') }}</span>
                            </li>
                            <li class="flex items-center gap-3 text-sm text-gray-600 font-normal {{ app()->getLocale() == 'ar' ? 'flex-row-reverse' : '' }}">
                                <i data-lucide="check" class="w-4 h-4 text-green-500 flex-shrink-0"></i>
                                <span>{{ $plan->max_users == 999 ? __('common.unlimited_users') : $plan->max_users . ' ' . __('common.users') }}</span>
                            </li>
                            <li class="flex items-center gap-3 text-sm text-gray-600 font-normal {{ app()->getLocale() == 'ar' ? 'flex-row-reverse' : '' }}">
                                <i data-lucide="check" class="w-4 h-4 text-green-500 flex-shrink-0"></i>
                                <span>{{ $plan->max_projects == 999 ? __('common.unlimited_projects') : $plan->max_projects . ' ' . __('common.projects') }}</span>
                            </li>
                        </ul>

                        <a href="/login" class="w-full py-3 rounded-lg text-center text-sm font-normal transition-all {{ $index === 1 ? 'theme-bg text-white shadow-lg theme-hover-bg' : 'bg-gray-50 text-gray-700 hover:bg-gray-100 border border-gray-100' }}">
                            {{ __('common.get_started') }}
                        </a>
                    </div>
                    @endforeach
                </div>

                <!-- Provider Plans -->
                <div class="contents" x-show="role === 'provider'" x-cloak>
                    @foreach($providerPlans as $index => $plan)
                    <div class="p-8 rounded-lg border {{ $index === 1 ? 'border-[#3da9e4] ring-1 ring-[#3da9e4]' : 'border-gray-100' }} bg-white flex flex-col {{ app()->getLocale() == 'ar' ? 'text-right' : '' }} relative">
                        @if($index === 1)
                            <span class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-[#3da9e4] text-white px-3 py-1 rounded-full text-[10px] font-normal uppercase tracking-wider">{{ __('common.most_popular') }}</span>
                        @endif
                        <h3 class="text-xl font-normal text-gray-900 mb-2">{{ $plan->name }}</h3>
                        <p class="text-sm text-gray-500 font-normal mb-6">{{ $plan->description ?? 'Built for growing service agencies.' }}</p>
                        
                        <div class="mb-8">
                            <span class="text-4xl font-normal text-gray-900" x-text="billing === 'monthly' ? '{{ number_format($plan->price) }}' : '{{ number_format($plan->price * 10) }}'"></span>
                            <span class="text-gray-400 font-normal">{{ __('common.sar') }}</span>
                            <span class="text-gray-400 text-sm font-normal">/ <span x-text="billing === 'monthly' ? '{{ __('common.mo') }}' : '{{ __('common.yr') }}'"></span></span>
                        </div>

                        <ul class="space-y-4 mb-10 flex-1">
                            <li class="flex items-center gap-3 text-sm text-gray-600 font-normal {{ app()->getLocale() == 'ar' ? 'flex-row-reverse' : '' }}">
                                <i data-lucide="check" class="w-4 h-4 text-green-500 flex-shrink-0"></i>
                                <span>{{ $plan->max_services == 999 ? __('common.unlimited_services') : $plan->max_services . ' ' . __('common.services') }}</span>
                            </li>
                            <li class="flex items-center gap-3 text-sm text-gray-600 font-normal {{ app()->getLocale() == 'ar' ? 'flex-row-reverse' : '' }}">
                                <i data-lucide="check" class="w-4 h-4 text-green-500 flex-shrink-0"></i>
                                <span>{{ $plan->max_users == 999 ? __('common.unlimited_users') : $plan->max_users . ' ' . __('common.users') }}</span>
                            </li>
                            <li class="flex items-center gap-3 text-sm text-gray-600 font-normal {{ app()->getLocale() == 'ar' ? 'flex-row-reverse' : '' }}">
                                <i data-lucide="check" class="w-4 h-4 text-green-500 flex-shrink-0"></i>
                                <span>{{ $plan->max_projects == 999 ? __('common.unlimited_projects') : $plan->max_projects . ' ' . __('common.projects') }}</span>
                            </li>
                        </ul>

                        <a href="/login" class="w-full py-3 rounded-lg text-center text-sm font-normal transition-all {{ $index === 1 ? 'theme-bg text-white shadow-lg theme-hover-bg' : 'bg-gray-50 text-gray-700 hover:bg-gray-100 border border-gray-100' }}">
                            {{ __('common.get_started') }}
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-16 px-6">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-12">
            <div class="col-span-1 md:col-span-1 space-y-6 {{ app()->getLocale() == 'ar' ? 'text-right' : '' }}">
                <img src="/images/logo/logo.png" class="h-8 object-contain {{ app()->getLocale() == 'ar' ? 'mr-0 ml-auto' : '' }}" alt="iGate Shared Services">
                <p class="text-gray-500 font-normal text-sm leading-relaxed">{{ __('common.footer_description') }}</p>
            </div>
            <div class="{{ app()->getLocale() == 'ar' ? 'text-right' : '' }}">
                <h4 class="font-normal text-gray-800 mb-6 text-sm uppercase tracking-wide">{{ __('common.marketplace') }}</h4>
                <ul class="space-y-4 text-sm text-gray-500 font-normal">
                    <li><a href="/login" class="hover:theme-text transition-colors">{{ __('common.service_catalog') }}</a></li>
                    <li><a href="/login" class="hover:theme-text transition-colors">{{ __('common.verified_providers') }}</a></li>
                    <li><a href="/login" class="hover:theme-text transition-colors">{{ __('common.enterprise_solutions') }}</a></li>
                </ul>
            </div>
            <div class="{{ app()->getLocale() == 'ar' ? 'text-right' : '' }}">
                <h4 class="font-normal text-gray-800 mb-6 text-sm uppercase tracking-wide">{{ __('common.governance') }}</h4>
                <ul class="space-y-4 text-sm text-gray-500 font-normal">
                    <li><a href="/terms" class="hover:theme-text transition-colors">{{ __('common.escrow_policy') }}</a></li>
                    <li><a href="/terms" class="hover:theme-text transition-colors">{{ __('common.terms_conditions') }}</a></li>
                    <li><a href="/terms" class="hover:theme-text transition-colors">{{ __('common.sla_framework') }}</a></li>
                </ul>
            </div>
            <div class="{{ app()->getLocale() == 'ar' ? 'text-right' : '' }}">
                <h4 class="font-normal text-gray-800 mb-6 text-sm uppercase tracking-wide">{{ __('common.support') }}</h4>
                <ul class="space-y-4 text-sm text-gray-500 font-normal">
                    <li><a href="mailto:support@igate.com" class="hover:theme-text transition-colors">support@igate.com</a></li>
                    <li><a href="#" class="hover:theme-text transition-colors">{{ __('common.help_center') }}</a></li>
                    <li><a href="#" class="hover:theme-text transition-colors">{{ __('common.api_documentation') }}</a></li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto pt-8 mt-12 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center text-gray-400 text-xs font-normal">
            <span>{{ __('common.copyright') }}</span>
            <div class="flex space-x-4 mt-4 md:mt-0 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }}">
                <a href="#" class="hover:theme-text"><i data-lucide="twitter" class="w-4 h-4"></i></a>
                <a href="#" class="hover:theme-text"><i data-lucide="linkedin" class="w-4 h-4"></i></a>
            </div>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
