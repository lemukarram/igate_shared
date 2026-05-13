<!DOCTYPE html>
<html :lang="lang" :dir="lang === 'ar' ? 'rtl' : 'ltr'" x-data="i18nManager()">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'iGate Shared Services') }}</title>
    <!-- Use Tailwind via CDN but with production-ready base setup -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3da9e4',
                        'primary-dark': '#2a8cc2',
                        'primary-light': '#ebf6fd',
                    }
                }
            }
        }
    </script>
    <style>
        @font-face { font-family: 'Poppins'; src: url('/fonts/Poppins/Poppins-Light.woff') format('woff'); font-weight: 300; }
        @font-face { font-family: 'Poppins'; src: url('/fonts/Poppins/Poppins-Regular.woff') format('woff'); font-weight: 400; }
        @font-face { font-family: 'Poppins'; src: url('/fonts/Poppins/Poppins-Regular.woff') format('woff'); font-weight: 500; }
        @font-face { font-family: 'Poppins'; src: url('/fonts/Poppins/Poppins-Regular.woff') format('woff'); font-weight: 600; }
        @font-face { font-family: 'Poppins'; src: url('/fonts/Poppins/Poppins-Regular.woff') format('woff'); font-weight: 700; }
        
        /* Restricted Border Radius and Theme Color Overrides */
        * { 
            border-radius: 0.5rem !important; 
        }
        svg {
            border-radius: 0px !important;
        }
        .rounded-full, .rounded-full * { border-radius: 9999px !important; }
        
        body { font-family: 'Poppins', sans-serif; background-color: #ffffff; font-weight: 300; }
        
        /* Global Font Weight Constraints */
        h1, h2, h3, h4, .font-normal, .font-normal, .font-normal { font-weight: 400 !important; }
        
        .bg-primary, .bg-blue-600, .bg-indigo-600 { background-color: #3da9e4 !important; }
        .text-primary, .text-blue-600, .text-indigo-600 { color: #3da9e4 !important; }
        .border-primary, .border-blue-600, .border-indigo-600 { border-color: #3da9e4 !important; }
        
        .sidebar-item { font-weight: 300 !important; }
        .sidebar-item * { font-weight: 300 !important; }
        .sidebar-item:hover { background-color: #f3f4f6; }
        .sidebar-item.active { background-color: #ebf6fd; color: #3da9e4; font-weight: 300 !important; }
        
        .pt-8.px-3.overflow-hidden * { font-weight: 300 !important; }
        .pt-8.px-3.overflow-hidden span.bg-primary { font-weight: 400 !important; } /* allow the count badge to be 400 if needed, or keep 300 */
        
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }

        /* Override large rounded classes specifically if they escape the wildcard */
        [class*="rounded-[2."], [class*="rounded-[3."], [class*="rounded-2xl"], [class*="rounded-3xl"] {
            border-radius: 0.5rem !important;
        }

        [dir="rtl"] .flip-rtl { transform: scaleX(-1); }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-white text-gray-900 overflow-hidden" x-init="init()">
    <div class="flex h-screen relative">
        <!-- Sidebar -->
        <div :class="sidebarCollapsed ? 'w-20' : 'w-64'" class="border-e border-gray-100 flex flex-col h-full bg-white transition-all duration-300 relative group">
            <!-- Collapse Toggle Button - Always Visible -->
            <button @click="sidebarCollapsed = !sidebarCollapsed" 
                    class="absolute -end-3 top-10 w-6 h-6 bg-white border border-gray-100 rounded-full flex items-center justify-center shadow-sm z-50 hover:bg-gray-50 transition-all">
                <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-gray-400 transition-transform" :class="sidebarCollapsed ? 'rotate-180' : ''"></i>
            </button>

            <div class="p-6 overflow-hidden">
                @php
                    $logoPath = $settings->logo;
                    $collapsedLogoPath = $settings->collapsed_logo;
                    $logoUrl = str_starts_with($logoPath, 'settings/') ? asset('storage/' . $logoPath) : asset($logoPath);
                    $collapsedLogoUrl = str_starts_with($collapsedLogoPath, 'settings/') ? asset('storage/' . $collapsedLogoPath) : asset($collapsedLogoPath);
                @endphp
                <img :src="sidebarCollapsed ? '{{ $collapsedLogoUrl }}' : '{{ $logoUrl }}'" alt="{{ $settings->site_name }}" class="h-10 w-auto object-contain min-w-[40px] transition-all duration-300">
            </div>

            <div class="px-6 mb-6 overflow-hidden">
                @if(Auth::user()->role === 'client')
                    <!-- <a href="{{ route('explore.index') }}" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-all font-normal shadow-sm">
                        <i data-lucide="plus" class="w-4 h-4 flex-shrink-0"></i>
                        <span x-show="!sidebarCollapsed" x-text="t('explore.request')" class="whitespace-nowrap"></span>
                    </a> -->
                @elseif(Auth::user()->role === 'provider')
                    <!-- <button @click="addServiceOpen = true" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-primary text-white rounded-[0.5rem] hover:bg-primary-dark transition-all font-normal shadow-sm">
                        <i data-lucide="plus" class="w-4 h-4 flex-shrink-0"></i>
                        <span x-show="!sidebarCollapsed" x-text="t('explore.add_to_portfolio')" class="whitespace-nowrap"></span>
                    </button> -->
                @endif
            </div>

            <nav class="flex-1 px-3 space-y-1 overflow-y-auto custom-scrollbar overflow-x-hidden">
                @if(Auth::user()->role === 'admin')
                    <a href="/" class="sidebar-item {{ request()->is('/') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 transition-all">
                        <i data-lucide="bar-chart-3" class="w-4 h-4 flex-shrink-0"></i>
                        <span x-show="!sidebarCollapsed" class="text-sm font-normal whitespace-nowrap" x-text="t('common.analytics')"></span>
                    </a>
                @elseif(Auth::user()->role === 'provider')
                    <a href="{{ route('explore.index') }}" class="sidebar-item {{ request()->routeIs('explore.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 transition-all">
                        <i data-lucide="compass" class="w-4 h-4 flex-shrink-0"></i>
                        <span x-show="!sidebarCollapsed" class="text-sm font-normal whitespace-nowrap" x-text="t('common.explore')"></span>
                    </a>

                    <a href="{{ route('provider.dashboard') }}" class="sidebar-item {{ request()->routeIs('provider.dashboard') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 transition-all">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 flex-shrink-0"></i>
                        <span x-show="!sidebarCollapsed" class="text-sm font-normal whitespace-nowrap" x-text="t('common.dashboard')"></span>
                    </a>
                    <a href="{{ route('provider.services.index') }}" class="sidebar-item {{ request()->routeIs('provider.services.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 transition-all">
                        <i data-lucide="briefcase" class="w-4 h-4 flex-shrink-0"></i>
                        <span x-show="!sidebarCollapsed" class="text-sm font-normal whitespace-nowrap" x-text="t('common.my_services')"></span>
                    </a>
                    
                    <a href="{{ route('provider.clients') }}" class="sidebar-item {{ request()->routeIs('provider.clients*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 transition-all">
                        <i data-lucide="users" class="w-4 h-4 flex-shrink-0"></i>
                        <span x-show="!sidebarCollapsed" class="text-sm font-normal whitespace-nowrap" x-text="t('common.clients')"></span>
                    </a>
                @elseif(Auth::user()->role === 'client')
                    <a href="{{ route('explore.index') }}" class="sidebar-item {{ request()->routeIs('explore.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 transition-all">
                        <i data-lucide="search" class="w-4 h-4 flex-shrink-0"></i>
                        <span x-show="!sidebarCollapsed" class="text-sm font-normal whitespace-nowrap" x-text="t('common.explore')"></span>
                    </a>

                    <a href="{{ route('client.dashboard') }}" class="sidebar-item {{ request()->routeIs('client.dashboard') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 transition-all">
                        <i data-lucide="home" class="w-4 h-4 flex-shrink-0"></i>
                        <span x-show="!sidebarCollapsed" class="text-sm font-normal whitespace-nowrap" x-text="t('common.dashboard')"></span>
                    </a>
                    <a href="{{ route('client.portfolio') }}" class="sidebar-item {{ request()->routeIs('client.portfolio') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 transition-all">
                        <i data-lucide="briefcase" class="w-4 h-4 flex-shrink-0"></i>
                        <span x-show="!sidebarCollapsed" class="text-sm font-normal whitespace-nowrap" x-text="t('common.portfolio')"></span>
                    </a>
                    <a href="{{ route('client.my_providers') }}" class="sidebar-item {{ request()->routeIs('client.my_providers') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 transition-all">
                        <i data-lucide="users" class="w-4 h-4 flex-shrink-0"></i>
                        <span x-show="!sidebarCollapsed" class="text-sm font-normal whitespace-nowrap" x-text="t('common.my_providers')"></span>
                    </a>
                    
                    
                @endif

                <div class="pt-8 px-3 overflow-hidden">
                    <div class="flex items-center justify-between text-[10px] font-normal uppercase tracking-widest text-gray-400 mb-4">
                        <span x-show="!sidebarCollapsed" x-text="t('common.projects')" class="whitespace-nowrap"></span>
                        <span class="bg-primary text-white px-2 py-0.5 rounded-md">{{ $ongoingProjects->count() }}</span>
                    </div>
                    <div class="space-y-1">
                        @foreach($ongoingProjects as $p)
                        <a href="{{ route('projects.show', $p->id) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-500 hover:bg-gray-50 transition-all {{ request()->is('projects/'.$p->id) ? 'bg-primary-light text-primary font-normal' : '' }}">
                            <div class="w-1.5 h-1.5 rounded-full bg-green-500 flex-shrink-0"></div>
                            <span x-show="!sidebarCollapsed" class="truncate text-xs font-normal whitespace-nowrap">{{ $p->service->name }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
            </nav>

            <!-- Bottom Profile -->
            <div class="p-4 border-t border-gray-100 relative ">
                <div @click="profileOpen = !profileOpen" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer transition-all">
                    <div class="w-8 h-8 bg-gradient-to-br from-primary to-primary-dark rounded-lg flex items-center justify-center text-white font-normal text-xs shadow-sm flex-shrink-0 overflow-hidden">
                        @if(Auth::user()->profile_picture)
                            <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" class="w-full bg-white h-full object-cover">
                        @else
                            {{ substr(Auth::user()->name, 0, 2) }}
                        @endif
                    </div>
                    <div class="flex-1 min-w-0" x-show="!sidebarCollapsed">
                        <p class="text-xs font-normal text-gray-900 truncate">{{ Auth::user()->name }}</p>
                    </div>
                    <i data-lucide="chevron-up" x-show="!sidebarCollapsed" class="w-4 h-4 text-gray-400 transition-transform" :class="profileOpen ? 'rotate-180' : ''"></i>
                </div>

                <!-- Submenu -->
                <div x-show="profileOpen" @click.away="profileOpen = false" 
                     :class="sidebarCollapsed ? 'bottom-20 start-16 w-48' : 'bottom-20 inset-x-4'"
                     class="absolute bg-white border border-gray-100 rounded-lg shadow-xl p-1 z-50 animate-in fade-in slide-in-from-bottom-2" style="display:none;">
                    <button @click="settingsOpen = true; profileOpen = false" class="w-full flex items-center gap-3 px-3 py-2 rounded-md text-gray-600 hover:bg-primary-light hover:text-primary transition-all">
                        <i data-lucide="settings" class="w-4 h-4"></i><span class="text-xs font-normal" x-text="t('common.settings')"></span>
                    </button>
                    <div class="my-1 border-t border-gray-50"></div>
                    <form action="{{ route('logout') }}" method="POST">@csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-md text-red-600 hover:bg-red-50 transition-all font-normal text-xs text-start">
                            <i data-lucide="log-out" class="w-4 h-4"></i><span x-text="t('common.logout')"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="flex-1 flex flex-col bg-white overflow-hidden">
            <main class="flex-1 overflow-y-auto p-10 flex flex-col items-center custom-scrollbar">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- MODALS SECTION -->
    <div x-show="settingsOpen" class="fixed inset-0 z-[100] flex items-center justify-center" style="display: none;">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="settingsOpen = false"></div>
        <div class="bg-white w-full max-w-4xl rounded-lg shadow-2xl relative z-10 overflow-hidden border border-gray-100 animate-in zoom-in duration-300">
            <div class="flex h-[550px]">
                <div class="w-56 bg-gray-50 border-r border-gray-100 p-8 flex flex-col">
                    <h3 class="text-[10px] font-normal uppercase tracking-widest text-gray-400 mb-8 px-2">{{ __('common.settings') }}</h3>
                    <!-- <div class="pb-4 px-4 overflow-hidden">
                        @php
                            $modalLogoPath = $settings->logo;
                            $modalLogoUrl = str_starts_with($modalLogoPath, 'settings/') ? asset('storage/' . $modalLogoPath) : asset($modalLogoPath);
                        @endphp
                        <img src="{{ $modalLogoUrl }}" alt="{{ $settings->site_name }}" class="h-10 w-auto object-contain min-w-[40px]">
                    </div> -->
                    <div class="space-y-1 flex-1">
                        <template x-for="t_tab in ['account', 'company', 'preferences', 'permissions', 'plans', 'notifications', 'security']">
                            <button @click="settingsTab = t_tab" 
                                    :class="settingsTab === t_tab ? 'bg-primary text-white font-normal' : 'text-gray-500 hover:bg-gray-100'" 
                                    class="w-full text-left px-4 py-2 rounded-md text-xs transition-all capitalize" 
                                    x-text="t('common.' + t_tab)"></button>
                        </template>
                    </div>
                </div>
                <div class="flex-1 p-8 flex flex-col">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-2xl font-normal text-gray-900 capitalize" x-text="settingsTab"></h2>
                        <button @click="settingsOpen = false" class="text-gray-400 hover:text-gray-900"><i data-lucide="x" class="w-5 h-5"></i></button>
                    </div>
                    <div class="space-y-6 flex-1 overflow-y-auto pr-4 custom-scrollbar">
                        <!-- Account Tab -->
                        <div x-show="settingsTab === 'account'" class="space-y-4 animate-in fade-in duration-300">
                            <form id="settings-account-form" action="{{ route('settings.profile') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 border-2 border-dashed border-gray-300 cursor-pointer hover:bg-gray-50 transition-colors relative overflow-hidden group">
                                        <img id="profile-preview" src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : '' }}" class="{{ Auth::user()->profile_picture ? '' : 'hidden' }} absolute inset-0 w-full h-full object-cover">
                                        <i data-lucide="upload" id="profile-upload-icon" class="{{ Auth::user()->profile_picture ? 'hidden' : '' }} w-6 h-6 group-hover:text-[#3da9e4] transition-colors"></i>
                                        <input type="file" name="profile_picture" class="absolute inset-0 opacity-0 cursor-pointer" onchange="previewImage(this, 'profile-preview', 'profile-upload-icon')">
                                    </div>
                                    <div>
                                        <p class="text-sm font-normal text-gray-900">Profile Picture</p>
                                        <p class="text-xs text-gray-500">Upload a professional photo (JPG, PNG)</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1"><label class="text-[10px] font-normal uppercase tracking-widest text-gray-400">First Name</label><input type="text" name="first_name" value="{{ explode(' ', Auth::user()->name)[0] }}" class="w-full px-4 py-2.5 border border-gray-100 bg-gray-50 rounded-lg outline-none focus:ring-4 focus:ring-primary/10 text-sm font-normal"></div>
                                    <div class="space-y-1"><label class="text-[10px] font-normal uppercase tracking-widest text-gray-400">Last Name</label><input type="text" name="last_name" value="{{ explode(' ', Auth::user()->name)[1] ?? '' }}" class="w-full px-4 py-2.5 border border-gray-100 bg-gray-50 rounded-lg outline-none focus:ring-4 focus:ring-primary/10 text-sm font-normal"></div>
                                </div>
                                <div class="space-y-1"><label class="text-[10px] font-normal uppercase tracking-widest text-gray-400">Email Address</label><input type="email" name="email" value="{{ Auth::user()->email }}" class="w-full px-4 py-2.5 border border-gray-100 bg-gray-50 rounded-lg outline-none focus:ring-4 focus:ring-primary/10 text-sm font-normal"></div>
                                <div class="space-y-1"><label class="text-[10px] font-normal uppercase tracking-widest text-gray-400">Phone Number</label><input type="text" name="phone" value="{{ Auth::user()->phone ?? '+966 50 000 0000' }}" class="w-full px-4 py-2.5 border border-gray-100 bg-gray-50 rounded-lg outline-none focus:ring-4 focus:ring-primary/10 text-sm font-normal"></div>
                            </form>
                        </div>

                        <!-- Company Profile Tab -->
                        <div x-show="settingsTab === 'company'" class="space-y-6 animate-in fade-in duration-300">
                            <form id="settings-company-form" action="{{ route('settings.company') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                                @csrf
                                <div class="flex items-center space-x-4 mb-2">
                                    <div class="w-20 h-20 bg-gray-50 rounded-lg flex items-center justify-center text-[#3da9e4] border border-gray-200 cursor-pointer hover:border-[#3da9e4] transition-all relative group overflow-hidden">
                                        @php
                                            $logo = Auth::user()->role === 'client' 
                                                ? (Auth::user()->companies()->first()->logo ?? null) 
                                                : (Auth::user()->providerProfile->logo ?? null);
                                        @endphp
                                        <img id="logo-preview" src="{{ $logo ? asset('storage/' . $logo) : '' }}" class="{{ $logo ? '' : 'hidden' }} absolute inset-0 w-full h-full object-contain p-2">
                                        <i data-lucide="image" id="logo-upload-icon" class="{{ $logo ? 'hidden' : '' }} w-6 h-6 group-hover:scale-110 transition-transform"></i>
                                        <input type="file" name="logo" class="absolute inset-0 opacity-0 cursor-pointer" onchange="previewImage(this, 'logo-preview', 'logo-upload-icon')">
                                    </div>
                                    <div>
                                        <p class="text-sm font-normal text-gray-900">Company Logo</p>
                                        <p class="text-xs text-gray-500">Used on invoices and marketplace</p>
                                    </div>
                                </div>
                                @if(Auth::user()->role === 'client')
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-normal uppercase tracking-widest text-gray-400">Company Name</label>
                                        <input type="text" name="name" value="{{ Auth::user()->companies()->first()->name ?? '' }}" class="w-full px-4 py-2.5 border border-gray-100 bg-gray-50 rounded-lg outline-none text-sm font-normal">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-normal uppercase tracking-widest text-gray-400">Industry</label>
                                        <select name="industry" class="tom-select w-full">
                                            @foreach(['Technology', 'Legal', 'Healthcare', 'Finance', 'Education', 'Construction', 'Retail'] as $ind)
                                                <option value="{{ $ind }}" {{ (Auth::user()->companies()->first()->industry ?? '') == $ind ? 'selected' : '' }}>{{ $ind }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endif
                                <div class="space-y-1">
                                    <label class="text-[10px] font-normal uppercase tracking-widest text-gray-400">About Company</label>
                                    <textarea name="about" rows="4" placeholder="Briefly describe your company, services, and industry..." class="w-full px-4 py-3 border border-gray-100 bg-gray-50 rounded-lg outline-none focus:ring-2 focus:ring-[#3da9e4]/50 focus:border-[#3da9e4] text-sm resize-none">{{ Auth::user()->role === 'client' ? (Auth::user()->companies()->first()->about ?? '') : (Auth::user()->providerProfile->bio ?? '') }}</textarea>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-normal uppercase tracking-widest text-gray-400">Company Documents</label>
                                    @if(Auth::user()->documents->where('project_id', null)->count() > 0)
                                        <div class="grid grid-cols-2 gap-2 mb-4">
                                            @foreach(Auth::user()->documents->where('project_id', null) as $doc)
                                                <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg border border-gray-100">
                                                    <i data-lucide="file-text" class="w-4 h-4 text-primary"></i>
                                                    <span class="text-[10px] font-normal text-gray-600 truncate flex-1">{{ $doc->name }}</span>
                                                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="text-primary hover:text-primary-dark transition-colors">
                                                        <i data-lucide="external-link" class="w-3 h-3"></i>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    <div class="w-full h-24 border-2 border-dashed border-gray-200 rounded-lg flex flex-col items-center justify-center cursor-pointer hover:bg-gray-50 transition-colors relative">
                                        <i data-lucide="upload-cloud" class="w-5 h-5 text-gray-400 mb-1"></i>
                                        <span class="text-xs font-normal text-gray-500">Upload Registration / Tax Certificates</span>
                                        <input type="file" name="documents[]" multiple class="absolute inset-0 opacity-0 cursor-pointer">
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Settings Tab -->
                        <div x-show="settingsTab === 'preferences'" class="space-y-6 animate-in fade-in duration-300">
                            <form id="settings-settings-form" action="{{ route('settings.general') }}" method="POST" class="space-y-6">
                                @csrf
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div><p class="text-sm font-normal text-gray-900">Dark Mode</p><p class="text-xs text-gray-400">Adjust the visual appearance</p></div>
                                    <div class="w-12 h-6 bg-gray-200 rounded-full relative"><div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow-sm"></div></div>
                                </div>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div><p class="text-sm font-normal text-gray-900">Language</p><p class="text-xs text-gray-400">System display language</p></div>
                                    <div class="flex items-center gap-1 bg-white border border-gray-100 p-1 rounded-lg">
                                        <button type="button" @click="setLang('ar')" :class="lang === 'ar' ? 'bg-primary text-white' : 'text-gray-400 hover:text-gray-600'" class="px-3 py-1 text-[10px] font-normal uppercase transition-all">ar</button>
                                        <button type="button" @click="setLang('en')" :class="lang === 'en' ? 'bg-primary text-white' : 'text-gray-400 hover:text-gray-600'" class="px-3 py-1 text-[10px] font-normal uppercase transition-all">en</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Permissions Tab -->
                        <div x-show="settingsTab === 'permissions'" class="space-y-6 animate-in fade-in duration-300">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-primary-light text-primary rounded-lg flex items-center justify-center"><i data-lucide="shield" class="w-5 h-5"></i></div>
                                    <div><p class="text-sm font-normal">Role: {{ ucfirst(Auth::user()->role) }}</p><p class="text-xs text-gray-400">Full access to dashboard and services</p></div>
                                </div>
                                <button @click="showAddUserForm = !showAddUserForm" class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-md text-xs font-normal hover:bg-primary-dark transition-all">
                                    <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                                    <span x-text="t('common.add_user')"></span>
                                </button>
                            </div>

                            <!-- Add User Form Section -->
                            <div x-show="showAddUserForm" x-collapse class="p-6 bg-gray-50 border border-gray-100 rounded-lg space-y-4 animate-in slide-in-from-top-2">
                                <h3 class="text-sm font-normal text-gray-900" x-text="t('common.add_user')"></h3>
                                <form action="{{ route('settings.team_members.store') }}" method="POST" class="space-y-4">
                                    @csrf
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-1">
                                            <label class="text-[10px] font-normal uppercase tracking-widest text-gray-400">Full Name</label>
                                            <input type="text" name="name" class="w-full px-4 py-2 border border-gray-200 rounded-md outline-none text-sm bg-white">
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-[10px] font-normal uppercase tracking-widest text-gray-400">Email Address</label>
                                            <input type="email" name="email" class="w-full px-4 py-2 border border-gray-200 rounded-md outline-none text-sm bg-white">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-1">
                                            <label class="text-[10px] font-normal uppercase tracking-widest text-gray-400">Role</label>
                                            <select name="role" class="tom-select w-full">
                                                <option value="manager">Manager</option>
                                                <option value="staff">Staff</option>
                                            </select>
                                        </div>
                                        <div class="col-span-2 bg-white p-3 rounded-lg border border-gray-200">
                                            <label class="text-[10px] font-normal uppercase tracking-widest text-gray-400 mb-2 block">Advanced Permissions</label>
                                            <div class="grid grid-cols-3 gap-3">
                                                <div x-data="{ scope: 'overall' }">
                                                    <label class="text-[9px] font-normal text-gray-500 uppercase">Scope</label>
                                                    <select name="permissions[0][scope]" x-model="scope" class="w-full px-2 py-1.5 border border-gray-200 rounded text-xs bg-gray-50 mt-1">
                                                        <option value="overall">Overall (All Access)</option>
                                                        <option value="company">Specific Company</option>
                                                        <option value="client">Specific Client</option>
                                                        <option value="project">Specific Project</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="text-[9px] font-normal text-gray-500 uppercase">Target</label>
                                                    <select name="permissions[0][target_id]" class="w-full px-2 py-1.5 border border-gray-200 rounded text-xs bg-gray-50 mt-1">
                                                        <option value="">-- Select Target --</option>
                                                        <template x-if="scope === 'company'">
                                                            @foreach($permission_companies ?? [] as $c)
                                                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                                            @endforeach
                                                        </template>
                                                        <template x-if="scope === 'client'">
                                                            @foreach($permission_clients ?? [] as $c)
                                                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                                            @endforeach
                                                        </template>
                                                        <template x-if="scope === 'project'">
                                                            @foreach($permission_projects ?? [] as $p)
                                                                <option value="{{ $p->id }}">{{ $p->service->name }} (PRJ-{{ $p->id }})</option>
                                                            @endforeach
                                                        </template>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="text-[9px] font-normal text-gray-500 uppercase">Action</label>
                                                    <select name="permissions[0][action]" class="w-full px-2 py-1.5 border border-gray-200 rounded text-xs bg-gray-50 mt-1">
                                                        <option value="edit">Edit (Full Control)</option>
                                                        <option value="view">View Only</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <p class="text-[10px] text-gray-400 mt-2 italic">Assign this member to a specific scope. Use Target ID to limit access to a single entity (e.g., Project ID: 1).</p>
                                        </div>
                                    </div>
                                    <div class="pt-2 flex justify-end gap-2">
                                        <button type="button" @click="showAddUserForm = false" class="px-4 py-2 text-gray-500 text-xs font-normal">Cancel</button>
                                        <button type="submit" class="px-6 py-2 bg-primary text-white rounded-md font-normal text-xs">Send Invitation</button>
                                    </div>
                                </form>
                            </div>

                            <form id="settings-permissions-form" action="{{ route('settings.team_members.update', 'multiple') }}" method="POST" class="space-y-4">
                                @csrf
                                @method('PATCH')
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <p class="text-[10px] font-normal uppercase tracking-widest text-gray-400" x-text="t('common.team_members')"></p>
                                    </div>
                                    <div class="overflow-hidden border border-gray-100 rounded-lg">
                                        <table class="w-full text-left text-sm">
                                            <thead class="bg-gray-50 text-[10px] uppercase tracking-widest text-gray-400">
                                                <tr class="divide-x divide-gray-100">
                                                    <th class="px-4 py-2 font-normal" x-text="t('common.name')"></th>
                                                    <th class="px-4 py-2 font-normal" x-text="t('common.status')"></th>
                                                    <th class="px-4 py-2 font-normal" x-text="t('common.role')"></th>
                                                    <th class="px-4 py-2 font-normal" x-text="t('common.permissions')"></th>
                                                    <th class="px-4 py-2 font-normal text-right" x-text="t('common.actions')"></th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-50">
                                                <tr class="hover:bg-gray-50 transition-all">
                                                    <td class="px-4 py-3 font-normal text-xs">{{ Auth::user()->name }}</td>
                                                    <td class="px-4 py-3">
                                                        <span class="px-2 py-0.5 bg-green-50 text-green-600 rounded-full text-[10px] font-normal" x-text="t('common.active')"></span>
                                                    </td>
                                                    <td class="px-4 py-3 capitalize text-xs">{{ Auth::user()->role }} (<span x-text="t('common.owner')"></span>)</td>
                                                    <td class="px-4 py-3"><span class="px-2 py-0.5 bg-primary-light text-primary rounded-full text-[10px] font-normal" x-text="t('common.full_access')"></span></td>
                                                    <td class="px-4 py-3 text-right"></td>
                                                </tr>
                                                @if(Auth::user()->ownedTeam && Auth::user()->ownedTeam->members)
                                                    @foreach(Auth::user()->ownedTeam->members as $member)
                                                    <tr class="hover:bg-gray-50 transition-all">
                                                        <td class="px-4 py-3 font-normal text-xs">
                                                            {{ $member->user->name ?? 'Invited' }}
                                                            @if(!$member->is_active)
                                                                <span class="text-[9px] font-normal uppercase text-red-500 ml-1" x-text="'(' + t('common.inactive') + ')'"></span>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-3">
                                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-normal {{ $member->is_active ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}" x-text="{{ $member->is_active ? 'true' : 'false' }} ? t('common.active') : t('common.inactive')"></span>
                                                        </td>
                                                        <td class="px-4 py-3">
                                                            <select name="members[{{ $member->id }}][role]" class="bg-transparent text-xs font-normal outline-none">
                                                                <option value="manager" {{ $member->role === 'manager' ? 'selected' : '' }} x-text="t('common.manager')"></option>
                                                                <option value="staff" {{ $member->role === 'staff' ? 'selected' : '' }} x-text="t('common.staff')"></option>
                                                            </select>
                                                        </td>
                                                        <td class="px-4 py-3">
                                                            <div class="flex flex-col gap-2" x-data="{ scope: '{{ $p['scope'] ?? 'overall' }}' }">
                                                                @php
                                                                    $perms = is_array($member->permissions) ? $member->permissions : [];
                                                                    $p = $perms[0] ?? ['scope' => 'overall', 'target_id' => '', 'action' => 'view'];
                                                                @endphp
                                                                <div class="grid grid-cols-3 gap-2">
                                                                    <select name="members[{{ $member->id }}][permissions][0][scope]" x-model="scope" class="w-full px-1.5 py-1 border border-gray-200 rounded text-[9px] bg-white">
                                                                        <option value="overall" {{ ($p['scope'] ?? '') === 'overall' ? 'selected' : '' }} x-text="t('common.overall')"></option>
                                                                        <option value="company" {{ ($p['scope'] ?? '') === 'company' ? 'selected' : '' }} x-text="t('common.company')"></option>
                                                                        <option value="client" {{ ($p['scope'] ?? '') === 'client' ? 'selected' : '' }} x-text="t('common.client')"></option>
                                                                        <option value="project" {{ ($p['scope'] ?? '') === 'project' ? 'selected' : '' }} x-text="t('common.project')"></option>
                                                                    </select>
                                                                    <select name="members[{{ $member->id }}][permissions][0][target_id]" class="w-full px-1.5 py-1 border border-gray-200 rounded text-[9px] bg-white">
                                                                        <option value="" x-text="t('common.select_target')"></option>
                                                                        <template x-if="scope === 'company'">
                                                                            @foreach($permission_companies ?? [] as $c)
                                                                                <option value="{{ $c->id }}" {{ ($p['target_id'] ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                                                            @endforeach
                                                                        </template>
                                                                        <template x-if="scope === 'client'">
                                                                            @foreach($permission_clients ?? [] as $c)
                                                                                <option value="{{ $c->id }}" {{ ($p['target_id'] ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                                                            @endforeach
                                                                        </template>
                                                                        <template x-if="scope === 'project'">
                                                                            @foreach($permission_projects ?? [] as $pj)
                                                                                <option value="{{ $pj->id }}" {{ ($p['target_id'] ?? '') == $pj->id ? 'selected' : '' }}>{{ $pj->service->name }}</option>
                                                                            @endforeach
                                                                        </template>
                                                                    </select>
                                                                    <select name="members[{{ $member->id }}][permissions][0][action]" class="w-full px-1.5 py-1 border border-gray-200 rounded text-[9px] bg-white">
                                                                        <option value="edit" {{ ($p['action'] ?? '') === 'edit' ? 'selected' : '' }} x-text="t('common.edit')"></option>
                                                                        <option value="view" {{ ($p['action'] ?? '') === 'view' ? 'selected' : '' }} x-text="t('common.view')"></option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-3 text-right">
                                                            <button type="button" @click="if(confirm(t('common.are_you_sure'))) { $refs['delete_member_' + {{ $member->id }}].submit() }" class="text-red-500 hover:text-red-700 transition-colors">
                                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </form>
                            @if(isset($teamMembers))
                                @foreach($teamMembers as $member)
                                    <form x-ref="delete_member_{{ $member->id }}" action="{{ route('settings.team_members.destroy', $member->id) }}" method="POST" class="hidden">
                                        @csrf @method('DELETE')
                                    </form>
                                @endforeach
                            @endif

                            <div class="space-y-2 pt-4">
                                <p class="text-[10px] font-normal uppercase tracking-widest text-gray-400" x-text="t('common.enabled_features')"></p>
                                <div class="flex flex-wrap gap-2">
                                    <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[10px] font-normal uppercase" x-text="t('common.payments')"></span>
                                    <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[10px] font-normal uppercase" x-text="t('common.service_request')"></span>
                                    <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[10px] font-normal uppercase" x-text="t('common.chat')"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Plans Tab -->
                        <div x-show="settingsTab === 'plans'" 
                             x-data="{ 
                                selectedPlanId: {{ Auth::user()->plan_id ?? 'null' }},
                                currentPlanId: {{ Auth::user()->plan_id ?? 'null' }},
                                plans: {{ \App\Models\Plan::where('type', Auth::user()->role)->get()->mapWithKeys(fn($p) => [$p->id => (float)$p->price])->toJson() }},
                                currentPrice: {{ Auth::user()->plan->price ?? 0 }},
                                get isUpgrade() {
                                    if (!this.selectedPlanId || this.selectedPlanId == this.currentPlanId) return false;
                                    return this.plans[this.selectedPlanId] > this.currentPrice;
                                },
                                get isDowngrade() {
                                    if (!this.selectedPlanId || this.selectedPlanId == this.currentPlanId) return false;
                                    return this.plans[this.selectedPlanId] < this.currentPrice;
                                }
                             }"
                             class="space-y-4 animate-in fade-in duration-300">
                            <div class="p-6 border border-primary/20 bg-primary-light rounded-xl">
                                <div class="flex items-center justify-between mb-4">
                                    <div><p class="text-[10px] font-normal text-primary uppercase tracking-widest mb-1" x-text="t('common.current_plan')"></p><h3 class="text-2xl font-normal text-gray-900">{{ Auth::user()->plan->name ?? '-' }}</h3></div>
                                    <span class="px-3 py-1 bg-primary text-white rounded-full text-[10px] font-normal uppercase" x-text="t('common.active')"></span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500 font-normal" x-text="t('common.plan_limits').replace(':max_services', '{{ Auth::user()->plan->max_services ?? 1 }}').replace(':max_projects', '{{ Auth::user()->plan->max_projects ?? 1 }}')"></span>
                                    <button type="button" class="text-primary font-normal uppercase tracking-widest text-xs" x-text="t('common.view_invoices')"></button>
                                </div>
                            </div>

                            <div x-show="isUpgrade" class="p-4 bg-blue-50 border border-blue-100 rounded-xl flex items-start space-x-3 animate-in slide-in-from-top duration-300">
                                <i data-lucide="info" class="w-5 h-5 text-blue-600 mt-0.5"></i>
                                <div>
                                    <p class="text-xs text-blue-800 font-normal" x-text="t('common.upgrade_notice')"></p>
                                </div>
                            </div>

                            <div x-show="isDowngrade" class="p-4 bg-amber-50 border border-amber-100 rounded-xl flex items-start space-x-3 animate-in slide-in-from-top duration-300">
                                <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 mt-0.5"></i>
                                <div>
                                    <p class="text-xs text-amber-800 font-normal" x-text="t('common.downgrade_notice')"></p>
                                </div>
                            </div>

                            <form id="settings-plans-form" action="{{ route('settings.plan') }}" method="POST" class="space-y-4 mt-6">
                                @csrf
                                <h4 class="text-sm font-normal" x-text="t('common.upgrade_plan')"></h4>
                                <div class="grid grid-cols-1 gap-3">
                                    @foreach(\App\Models\Plan::where('type', Auth::user()->role)->get() as $plan)
                                    <label class="flex items-center justify-between p-4 border rounded-lg cursor-pointer transition-all hover:bg-gray-50"
                                           :class="selectedPlanId == {{ $plan->id }} ? 'border-primary bg-primary-light' : 'border-gray-200'">
                                        <div class="flex items-center space-x-3 w-full">
                                            <input type="radio" name="plan_id" value="{{ $plan->id }}" 
                                                   x-model="selectedPlanId"
                                                   {{ Auth::user()->plan_id == $plan->id ? 'checked' : '' }} class="w-4 h-4 text-primary focus:ring-primary">
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between">
                                                    <p class="font-normal text-sm">{{ $plan->name }}</p>
                                                    <p class="font-normal text-sm">
                                                        @if($plan->price > 0)
                                                            {{ number_format($plan->price, 0) }} {{ __('common.sar') }}
                                                        @elseif($plan->price == 0 && strtolower($plan->name) === 'enterprise')
                                                            {{ __('common.custom') }}
                                                        @else
                                                            {{ __('common.free') }}
                                                        @endif
                                                    </p>
                                                </div>
                                                @if($plan->description)
                                                    <p class="text-[10px] text-gray-400 mb-1">{{ $plan->description }}</p>
                                                @endif
                                                <p class="text-xs text-gray-500" x-text="t('common.up_to') + ' ' + '{{ $plan->max_services }}' + ' ' + t('common.services')"></p>
                                                @if($plan->features && is_array($plan->features))
                                                    <div class="mt-2 flex flex-wrap gap-1">
                                                        @foreach($plan->features as $feature)
                                                            <span class="px-1.5 py-0.5 bg-gray-100 text-[9px] text-gray-500 rounded flex items-center gap-1">
                                                                <i data-lucide="check" class="w-2.5 h-2.5 text-green-500"></i>
                                                                {{ is_array($feature) ? ($feature['feature'] ?? '') : $feature }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                                <div class="pt-6">
                                    <button type="submit" 
                                            x-show="selectedPlanId != currentPlanId"
                                            class="w-full py-4 bg-primary text-white rounded-xl font-normal hover:bg-primary-dark transition-all shadow-xl shadow-primary/10 flex items-center justify-center space-x-2">
                                        <span x-text="t('common.update_plan')"></span>
                                        <i data-lucide="arrow-right" class="w-5 h-5 ml-2"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Notifications Tab -->
                        <div x-show="settingsTab === 'notifications'" class="space-y-4 animate-in fade-in duration-300">
                            <form id="settings-notifications-form" action="{{ route('settings.notifications') }}" method="POST" class="space-y-4">
                                @csrf
                                <div class="flex items-center justify-between p-4 border-b border-gray-50">
                                    <span class="text-sm font-normal text-gray-700" x-text="t('common.email_notifications')"></span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="notifications[email]" value="1" {{ (Auth::user()->notification_settings['email'] ?? false) ? 'checked' : '' }} class="sr-only peer">
                                        <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between p-4 border-b border-gray-50">
                                    <span class="text-sm font-normal text-gray-700" x-text="t('common.browser_push')"></span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="notifications[push]" value="1" {{ (Auth::user()->notification_settings['push'] ?? false) ? 'checked' : '' }} class="sr-only peer">
                                        <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between p-4 border-b border-gray-50">
                                    <span class="text-sm font-normal text-gray-700" x-text="t('common.sms_alerts')"></span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="notifications[sms]" value="1" {{ (Auth::user()->notification_settings['sms'] ?? false) ? 'checked' : '' }} class="sr-only peer">
                                        <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between p-4 border-b border-gray-50">
                                    <span class="text-sm font-normal text-gray-700" x-text="t('common.marketing_inquiries')"></span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="notifications[marketing]" value="1" {{ (Auth::user()->notification_settings['marketing'] ?? false) ? 'checked' : '' }} class="sr-only peer">
                                        <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                                    </label>
                                </div>
                            </form>
                        </div>

                        <!-- Security Tab -->
                        <div x-show="settingsTab === 'security'" class="space-y-4 animate-in fade-in duration-300">
                            <form id="settings-security-form" action="{{ route('settings.security') }}" method="POST" class="space-y-4">
                                @csrf
                                <div class="space-y-1">
                                    <label class="text-[10px] font-normal uppercase tracking-widest text-gray-400">Current Password</label>
                                    <input type="password" name="current_password" class="w-full px-4 py-2.5 border border-gray-100 bg-gray-50 rounded-lg outline-none text-sm font-normal">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-normal uppercase tracking-widest text-gray-400">New Password</label>
                                    <input type="password" name="password" class="w-full px-4 py-2.5 border border-gray-100 bg-gray-50 rounded-lg outline-none text-sm font-normal">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-normal uppercase tracking-widest text-gray-400">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" class="w-full px-4 py-2.5 border border-gray-100 bg-gray-50 rounded-lg outline-none text-sm font-normal">
                                </div>
                                <div class="flex items-center space-x-3 p-4 bg-amber-50 rounded-lg text-amber-700 border border-amber-100">
                                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                                    <p class="text-xs font-normal">Two-factor authentication is currently disabled.</p>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="pt-6 border-t border-gray-50 flex justify-end">
                        <button @click="submitSettingsForm()" class="px-6 py-2 bg-gray-900 text-white rounded-md font-normal text-xs hover:bg-black transition-all" x-text="t('common.save')"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ADD SERVICE MODAL -->
    @if(Auth::user()->role === 'provider')
    <div x-show="addServiceOpen" class="fixed inset-0 z-[100] flex items-center justify-center" style="display: none;">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-md" @click="addServiceOpen = false"></div>
        <div class="bg-white w-full max-w-lg rounded-lg shadow-2xl relative z-10 p-10 border border-gray-100 animate-in zoom-in duration-300">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-normal" x-text="t('explore.add_service')"></h2>
                <button @click="addServiceOpen = false" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <form action="{{ route('provider.services.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="space-y-1">
                    <label class="text-[10px] font-normal uppercase tracking-widest text-gray-400" x-text="t('explore.service_catalog')"></label>
                    <div class="relative">
                        <select name="service_id" class="w-full px-4 py-2.5 border border-gray-200 bg-white rounded-md text-sm font-normal text-gray-700 appearance-none cursor-pointer focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all">
                            @foreach(\App\Models\Service::all() as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                        <!-- Custom arrow -->
                        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                            <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-normal uppercase tracking-widest text-gray-400" x-text="t('explore.price')"></label>
                        <input type="number" name="price" step="0.01" class="w-full px-4 py-2.5 border border-gray-200 bg-white rounded-md text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-normal uppercase tracking-widest text-gray-400" x-text="t('explore.days')"></label>
                        <input type="number" name="delivery_time_days" class="w-full px-4 py-2.5 border border-gray-200 bg-white rounded-md text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all">
                    </div>
                </div>
                <button type="submit" class="w-full py-4 bg-primary text-white rounded-md font-normal" x-text="t('explore.add_to_portfolio_btn')"></button>
            </form>
        </div>
    </div>
    @endif

    <!-- Toast Notifications -->
    <div x-data="toastManager()" 
         @toast.window="add($event.detail)" 
         class="fixed top-5 right-5 z-[200] flex flex-col gap-3 pointer-events-none">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="toast.visible" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="translate-x-full opacity-0"
                 x-transition:enter-end="translate-x-0 opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-x-0 opacity-100"
                 x-transition:leave-end="translate-x-full opacity-0"
                 :class="toast.type === 'success' ? 'bg-white border-green-100' : 'bg-white border-red-100'"
                 class="pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-lg shadow-xl border min-w-[300px] max-w-md animate-in slide-in-from-right duration-300">
                <div :class="toast.type === 'success' ? 'bg-green-50 text-green-500' : 'bg-red-50 text-red-500'" class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0">
                    <i :data-lucide="toast.type === 'success' ? 'check-circle' : 'alert-circle'" class="w-5 h-5"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-normal text-gray-900" x-text="toast.title"></p>
                    <p class="text-[10px] text-gray-500 mt-0.5" x-text="toast.message"></p>
                </div>
                <button @click="remove(toast.id)" class="text-gray-400 hover:text-gray-900 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </template>
    </div>

    <script>
        function toastManager() {
            return {
                toasts: [],
                add(detail) {
                    const id = Date.now();
                    this.toasts.push({
                        id: id,
                        visible: true,
                        type: detail.type || 'success',
                        title: detail.title || (detail.type === 'success' ? 'Success' : 'Error'),
                        message: detail.message
                    });
                    setTimeout(() => {
                        this.remove(id);
                    }, 5000);
                    setTimeout(() => lucide.createIcons(), 50);
                },
                remove(id) {
                    const toast = this.toasts.find(t => t.id === id);
                    if (toast) {
                        toast.visible = false;
                        setTimeout(() => {
                            this.toasts = this.toasts.filter(t => t.id !== id);
                        }, 300);
                    }
                }
            }
        }

        function i18nManager() {
            return {
                lang: '{{ App::getLocale() }}',
                settingsOpen: false,
                addServiceOpen: false,
                showAddUserForm: false,
                settingsTab: 'account',
                profileOpen: false,
                sidebarCollapsed: localStorage.getItem('sidebar_collapsed') === 'true',
                dict: {
                    en: {
                        common: @json(Lang::get('common', [], 'en')),
                        explore: @json(Lang::get('explore', [], 'en')),
                        project: @json(Lang::get('project', [], 'en')),
                        tasks: @json(Lang::get('tasks', [], 'en'))
                    },
                    ar: {
                        common: @json(Lang::get('common', [], 'ar')),
                        explore: @json(Lang::get('explore', [], 'ar')),
                        project: @json(Lang::get('project', [], 'ar')),
                        tasks: @json(Lang::get('tasks', [], 'ar'))
                    }
                },
                t(key) {
                    const keys = key.split('.');
                    let result = this.dict[this.lang];
                    for (const k of keys) {
                        result = result ? result[k] : null;
                    }
                    return result || key;
                },
                setLang(newLang) {
                    if (this.lang === newLang) return;
                    localStorage.setItem('igate_lang', newLang);
                    document.cookie = "igate_lang=" + newLang + ";path=/;max-age=" + (365 * 24 * 60 * 60);
                    location.reload();
                },
                init() {
                    lucide.createIcons();
                    this.$watch('sidebarCollapsed', value => {
                        localStorage.setItem('sidebar_collapsed', value);
                        setTimeout(() => lucide.createIcons(), 300);
                    });

                    this.$watch('settingsOpen', value => {
                        if (value) {
                            setTimeout(() => {
                                document.querySelectorAll('.tom-select').forEach(el => {
                                    if (!el.tomselect) {
                                        new TomSelect(el, {
                                            create: false,
                                            sortField: {
                                                field: "text",
                                                direction: "asc"
                                            }
                                        });
                                    }
                                });
                            }, 100);
                        }
                    });
                },
                submitSettingsForm() {
                    const activeTab = this.settingsTab;
                    const form = document.querySelector(`#settings-${activeTab}-form`);
                    if (form) {
                        form.submit();
                    } else {
                        // fallback for tabs that might not have a form or use different IDs
                        const visibleForm = document.querySelector(`div[x-show="settingsTab === '${activeTab}'"] form`);
                        if (visibleForm) visibleForm.submit();
                    }
                }
            }
        }

        function previewImage(input, previewId, iconId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById(previewId);
                    const icon = document.getElementById(iconId);
                    if (preview) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                    }
                    if (icon) {
                        icon.classList.add('hidden');
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

    @if(session('success'))
        <script>
            window.addEventListener('load', () => {
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: "{{ session('success') }}" } }));
            });
        </script>
    @endif
    @if(session('error'))
        <script>
            window.addEventListener('load', () => {
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: "{{ session('error') }}" } }));
            });
        </script>
    @endif
    @if($errors->any())
        <script>
            window.addEventListener('load', () => {
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: "{{ $errors->first() }}" } }));
            });
        </script>
    @endif
</body>
</html>
