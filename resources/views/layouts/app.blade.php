<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'iGate Shared Services') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
        @font-face { font-family: 'Poppins'; src: url('/fonts/Poppins/Poppins-Light.ttf') format('truetype'); font-weight: 300; }
        @font-face { font-family: 'Poppins'; src: url('/fonts/Poppins/Poppins-Regular.ttf') format('truetype'); font-weight: 400; }
        @font-face { font-family: 'Poppins'; src: url('/fonts/Poppins/Poppins-Medium.ttf') format('truetype'); font-weight: 500; }
        
        /* Restricted Border Radius and Theme Color Overrides */
        * { 
            border-radius: 0.25rem !important; 
        }
        .rounded-full, .rounded-full * { border-radius: 9999px !important; }
        
        body { font-family: 'Poppins', sans-serif; background-color: #ffffff; font-weight: 300; }
        h1, h2, h3, h4, .font-bold { font-weight: 500 !important; }
        
        .bg-primary, .bg-blue-600, .bg-indigo-600 { background-color: #3da9e4 !important; }
        .text-primary, .text-blue-600, .text-indigo-600 { color: #3da9e4 !important; }
        .border-primary, .border-blue-600, .border-indigo-600 { border-color: #3da9e4 !important; }
        
        .sidebar-item:hover { background-color: #f3f4f6; }
        .sidebar-item.active { background-color: #ebf6fd; color: #3da9e4; font-weight: 500; }
        
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }

        /* Override large rounded classes specifically if they escape the wildcard */
        [class*="rounded-[2."], [class*="rounded-[3."], [class*="rounded-2xl"], [class*="rounded-3xl"] {
            border-radius: 0.5rem !important;
        }
    </style>
</head>
<body class="bg-white text-gray-900 overflow-hidden" x-data="{ 
    settingsOpen: false, 
    addServiceOpen: false, 
    viewUsersOpen: false, 
    addUserOpen: false,
    settingsTab: 'account',
    profileOpen: false
}">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 border-r border-gray-100 flex flex-col h-full bg-white">
            <div class="p-6">
                <img src="/images/logo/logo.png" alt="iGate Shared Services" class="h-10 w-auto object-contain">
            </div>

            <div class="px-6 mb-6">
                @if(Auth::user()->role === 'client')
                    <a href="{{ route('explore.index') }}" class="w-full flex items-center justify-center space-x-2 px-4 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-all font-medium shadow-sm">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Request Service</span>
                    </a>
                @elseif(Auth::user()->role === 'provider')
                    <button @click="addServiceOpen = true" class="w-full flex items-center justify-center space-x-2 px-4 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-all font-medium shadow-sm">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Add Service</span>
                    </button>
                @endif
            </div>

            <nav class="flex-1 px-3 space-y-1 overflow-y-auto custom-scrollbar">
                @if(Auth::user()->role === 'admin')
                    <a href="/" class="sidebar-item {{ request()->is('/') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2.5 rounded-lg text-gray-600 transition-all">
                        <i data-lucide="bar-chart-3" class="w-4 h-4"></i><span class="text-sm font-medium">Analytics</span>
                    </a>
                @elseif(Auth::user()->role === 'provider')
                    <a href="/provider/dashboard" class="sidebar-item {{ request()->routeIs('provider.dashboard') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2.5 rounded-lg text-gray-600 transition-all">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i><span class="text-sm font-medium">Dashboard</span>
                    </a>
                    <a href="{{ route('provider.services.index') }}" class="sidebar-item {{ request()->routeIs('provider.services.*') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2.5 rounded-lg text-gray-600 transition-all">
                        <i data-lucide="briefcase" class="w-4 h-4"></i><span class="text-sm font-medium">My Services</span>
                    </a>
                    <a href="{{ route('explore.index') }}" class="sidebar-item {{ request()->routeIs('explore.*') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2.5 rounded-lg text-gray-600 transition-all">
                        <i data-lucide="compass" class="w-4 h-4"></i><span class="text-sm font-medium">Explore</span>
                    </a>
                    <a href="{{ route('provider.clients') }}" class="sidebar-item {{ request()->routeIs('provider.clients*') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2.5 rounded-lg text-gray-600 transition-all">
                        <i data-lucide="users" class="w-4 h-4"></i><span class="text-sm font-medium">My Clients</span>
                    </a>
                    <a href="{{ route('provider.team_tasks') }}" class="sidebar-item {{ request()->routeIs('provider.team_tasks*') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2.5 rounded-lg text-gray-600 transition-all">
                        <i data-lucide="check-square" class="w-4 h-4"></i><span class="text-sm font-medium">Team Tasks</span>
                    </a>
                @elseif(Auth::user()->role === 'client')
                    <a href="/" class="sidebar-item {{ request()->is('/') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2.5 rounded-lg text-gray-600 transition-all">
                        <i data-lucide="home" class="w-4 h-4"></i><span class="text-sm font-medium">Dashboard</span>
                    </a>
                    <a href="{{ route('client.portfolio') }}" class="sidebar-item {{ request()->routeIs('client.portfolio') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2.5 rounded-lg text-gray-600 transition-all">
                        <i data-lucide="briefcase" class="w-4 h-4"></i><span class="text-sm font-medium">Portfolio</span>
                    </a>
                    <a href="{{ route('explore.index') }}" class="sidebar-item {{ request()->routeIs('explore.*') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2.5 rounded-lg text-gray-600 transition-all">
                        <i data-lucide="search" class="w-4 h-4"></i><span class="text-sm font-medium">Explore</span>
                    </a>
                    <a href="#" class="sidebar-item flex items-center space-x-3 px-3 py-2.5 rounded-lg text-gray-600 transition-all">
                        <i data-lucide="message-square" class="w-4 h-4"></i><span class="text-sm font-medium">Messages</span>
                    </a>
                @endif

                <div class="pt-8 px-3">
                    <div class="flex items-center justify-between text-[10px] font-medium uppercase tracking-widest text-gray-400 mb-4">
                        <span>Active Projects</span>
                        <span class="bg-primary text-white px-2 py-0.5 rounded-md">{{ $ongoingProjects->count() }}</span>
                    </div>
                    <div class="space-y-1">
                        @foreach($ongoingProjects as $p)
                        <a href="{{ route('projects.show', $p->id) }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-500 hover:bg-gray-50 transition-all {{ request()->is('projects/'.$p->id) ? 'bg-primary-light text-primary font-medium' : '' }}">
                            <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                            <span class="truncate text-xs font-medium">{{ $p->service->name }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
            </nav>

            <!-- Bottom Profile -->
            <div class="p-4 border-t border-gray-100 relative">
                <div @click="profileOpen = !profileOpen" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer transition-all">
                    <div class="w-8 h-8 bg-gradient-to-br from-primary to-primary-dark rounded-lg flex items-center justify-center text-white font-medium text-xs shadow-sm">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                    </div>
                    <i data-lucide="chevron-up" class="w-4 h-4 text-gray-400 transition-transform" :class="profileOpen ? 'rotate-180' : ''"></i>
                </div>

                <!-- Submenu -->
                <div x-show="profileOpen" @click.away="profileOpen = false" 
                     class="absolute bottom-20 left-4 w-52 bg-white border border-gray-100 rounded-lg shadow-xl p-1 z-50 animate-in fade-in slide-in-from-bottom-2" style="display:none;">
                    <button @click="settingsOpen = true; profileOpen = false" class="w-full flex items-center space-x-3 px-3 py-2 rounded-md text-gray-600 hover:bg-primary-light hover:text-primary transition-all">
                        <i data-lucide="settings" class="w-4 h-4"></i><span class="text-xs font-medium">Settings</span>
                    </button>
                    <button @click="viewUsersOpen = true; profileOpen = false" class="w-full flex items-center space-x-3 px-3 py-2 rounded-md text-gray-600 hover:bg-primary-light hover:text-primary transition-all">
                        <i data-lucide="users-2" class="w-4 h-4"></i><span class="text-xs font-medium">View Users</span>
                    </button>
                    <button @click="addUserOpen = true; profileOpen = false" class="w-full flex items-center space-x-3 px-3 py-2 rounded-md text-gray-600 hover:bg-primary-light hover:text-primary transition-all">
                        <i data-lucide="user-plus" class="w-4 h-4"></i><span class="text-xs font-medium">Add User</span>
                    </button>
                    <div class="my-1 border-t border-gray-50"></div>
                    <form action="{{ route('logout') }}" method="POST">@csrf
                        <button type="submit" class="w-full flex items-center space-x-3 px-3 py-2 rounded-md text-red-600 hover:bg-red-50 transition-all font-medium text-xs text-left">
                            <i data-lucide="log-out" class="w-4 h-4"></i><span>Logout</span>
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
                    <h3 class="text-[10px] font-medium uppercase tracking-widest text-gray-400 mb-8 px-2">Settings</h3>
                    <div class="space-y-1 flex-1">
                        <template x-for="t in ['account', 'company', 'settings', 'permissions', 'plans', 'notifications', 'security']">
                            <button @click="settingsTab = t" 
                                    :class="settingsTab === t ? 'bg-primary text-white font-medium' : 'text-gray-500 hover:bg-gray-100'" 
                                    class="w-full text-left px-4 py-2 rounded-md text-xs transition-all capitalize" 
                                    x-text="t"></button>
                        </template>
                    </div>
                </div>
                <div class="flex-1 p-10 flex flex-col">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-2xl font-medium text-gray-900 capitalize" x-text="settingsTab"></h2>
                        <button @click="settingsOpen = false" class="text-gray-400 hover:text-gray-900"><i data-lucide="x" class="w-5 h-5"></i></button>
                    </div>
                    <div class="space-y-6 flex-1 overflow-y-auto pr-4 custom-scrollbar">
                        <!-- Account Tab -->
                        <div x-show="settingsTab === 'account'" class="space-y-4 animate-in fade-in duration-300">
                            <form action="{{ route('settings.profile') }}" method="POST" class="space-y-4">
                                @csrf
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 border-2 border-dashed border-gray-300 cursor-pointer hover:bg-gray-50 transition-colors relative overflow-hidden group">
                                        <i data-lucide="upload" class="w-6 h-6 group-hover:text-[#3da9e4] transition-colors"></i>
                                        <input type="file" name="profile_picture" class="absolute inset-0 opacity-0 cursor-pointer">
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">Profile Picture</p>
                                        <p class="text-xs text-gray-500">Upload a professional photo (JPG, PNG)</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1"><label class="text-[10px] font-black uppercase tracking-widest text-gray-400">First Name</label><input type="text" name="first_name" value="{{ explode(' ', Auth::user()->name)[0] }}" class="w-full px-4 py-2.5 border border-gray-100 bg-gray-50 rounded-lg outline-none focus:ring-4 focus:ring-primary/10 text-sm font-medium"></div>
                                    <div class="space-y-1"><label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Last Name</label><input type="text" name="last_name" value="{{ explode(' ', Auth::user()->name)[1] ?? '' }}" class="w-full px-4 py-2.5 border border-gray-100 bg-gray-50 rounded-lg outline-none focus:ring-4 focus:ring-primary/10 text-sm font-medium"></div>
                                </div>
                                <div class="space-y-1"><label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Email Address</label><input type="email" name="email" value="{{ Auth::user()->email }}" class="w-full px-4 py-2.5 border border-gray-100 bg-gray-50 rounded-lg outline-none focus:ring-4 focus:ring-primary/10 text-sm font-medium"></div>
                                <div class="space-y-1"><label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Phone Number</label><input type="text" name="phone" value="{{ Auth::user()->phone ?? '+966 50 000 0000' }}" class="w-full px-4 py-2.5 border border-gray-100 bg-gray-50 rounded-lg outline-none focus:ring-4 focus:ring-primary/10 text-sm font-medium"></div>
                                <div class="pt-2 flex justify-end">
                                    <button type="submit" class="px-6 py-2 bg-[#3da9e4] text-white rounded-md font-medium text-xs hover:bg-[#2b8bc2] transition-all">Save Account</button>
                                </div>
                            </form>
                        </div>

                        <!-- Company Profile Tab -->
                        <div x-show="settingsTab === 'company'" class="space-y-6 animate-in fade-in duration-300">
                            <div class="flex items-center space-x-4 mb-2">
                                <div class="w-20 h-20 bg-gray-50 rounded-lg flex items-center justify-center text-[#3da9e4] border border-gray-200 cursor-pointer hover:border-[#3da9e4] transition-all relative group overflow-hidden">
                                    <i data-lucide="image" class="w-6 h-6 group-hover:scale-110 transition-transform"></i>
                                    <input type="file" class="absolute inset-0 opacity-0 cursor-pointer">
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Company Logo</p>
                                    <p class="text-xs text-gray-500">Used on invoices and marketplace</p>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">About Company</label>
                                <textarea rows="4" placeholder="Briefly describe your company, services, and industry..." class="w-full px-4 py-3 border border-gray-100 bg-gray-50 rounded-lg outline-none focus:ring-2 focus:ring-[#3da9e4]/50 focus:border-[#3da9e4] text-sm resize-none"></textarea>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Company Documents</label>
                                <div class="w-full h-24 border-2 border-dashed border-gray-200 rounded-lg flex flex-col items-center justify-center cursor-pointer hover:bg-gray-50 transition-colors relative">
                                    <i data-lucide="upload-cloud" class="w-5 h-5 text-gray-400 mb-1"></i>
                                    <span class="text-xs font-medium text-gray-500">Upload Registration / Tax Certificates</span>
                                    <input type="file" multiple class="absolute inset-0 opacity-0 cursor-pointer">
                                </div>
                            </div>
                        </div>

                        <!-- Settings Tab -->
                        <div x-show="settingsTab === 'settings'" class="space-y-6 animate-in fade-in duration-300">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div><p class="text-sm font-bold text-gray-900">Dark Mode</p><p class="text-xs text-gray-400">Adjust the visual appearance</p></div>
                                <div class="w-12 h-6 bg-gray-200 rounded-full relative"><div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow-sm"></div></div>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div><p class="text-sm font-bold text-gray-900">Language</p><p class="text-xs text-gray-400">System display language</p></div>
                                <select class="bg-transparent text-sm font-bold text-primary outline-none"><option>English</option><option>Arabic</option></select>
                            </div>
                        </div>

                        <!-- Permissions Tab -->
                        <div x-show="settingsTab === 'permissions'" class="space-y-4 animate-in fade-in duration-300">
                            <div class="p-4 border border-gray-100 rounded-lg flex items-center space-x-4">
                                <div class="w-10 h-10 bg-primary-light text-primary rounded-lg flex items-center justify-center"><i data-lucide="shield" class="w-5 h-5"></i></div>
                                <div><p class="text-sm font-bold">Role: {{ ucfirst(Auth::user()->role) }}</p><p class="text-xs text-gray-400">Full access to dashboard and services</p></div>
                            </div>
                            <div class="space-y-2">
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Enabled Features</p>
                                <div class="flex flex-wrap gap-2">
                                    <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[10px] font-black uppercase">Payments</span>
                                    <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[10px] font-black uppercase">Service Request</span>
                                    <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[10px] font-black uppercase">Chat</span>
                                </div>
                            </div>
                        </div>

                        <!-- Plans Tab -->
                        <div x-show="settingsTab === 'plans'" class="space-y-4 animate-in fade-in duration-300">
                            <div class="p-6 border border-primary/20 bg-primary-light rounded-xl">
                                <div class="flex items-center justify-between mb-4">
                                    <div><p class="text-[10px] font-black text-primary uppercase tracking-widest mb-1">Current Plan</p><h3 class="text-2xl font-black text-gray-900">{{ Auth::user()->plan->name ?? 'Basic' }}</h3></div>
                                    <span class="px-3 py-1 bg-primary text-white rounded-full text-[10px] font-black uppercase">Active</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500 font-medium">Limits: {{ Auth::user()->plan->max_services ?? 1 }} Services, {{ Auth::user()->plan->max_projects ?? 1 }} Projects</span>
                                    <button class="text-primary font-black uppercase tracking-widest text-xs">View Invoices</button>
                                </div>
                            </div>
                            <form action="{{ route('settings.plan') }}" method="POST" class="space-y-4 mt-6">
                                @csrf
                                <h4 class="text-sm font-bold">Upgrade Plan</h4>
                                <div class="grid grid-cols-1 gap-3">
                                    @foreach(\App\Models\Plan::where('type', Auth::user()->role)->get() as $plan)
                                    <label class="flex items-center justify-between p-4 border rounded-lg cursor-pointer transition-all hover:bg-gray-50 {{ Auth::user()->plan_id == $plan->id ? 'border-primary bg-primary-light' : 'border-gray-200' }}">
                                        <div class="flex items-center space-x-3">
                                            <input type="radio" name="plan_id" value="{{ $plan->id }}" {{ Auth::user()->plan_id == $plan->id ? 'checked' : '' }} class="w-4 h-4 text-primary focus:ring-primary">
                                            <div>
                                                <p class="font-bold text-sm">{{ $plan->name }}</p>
                                                <p class="text-xs text-gray-500">Up to {{ $plan->max_services }} services</p>
                                            </div>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                                <div class="pt-2 flex justify-end">
                                    <button type="submit" class="px-6 py-2 bg-[#3da9e4] text-white rounded-md font-medium text-xs hover:bg-[#2b8bc2] transition-all">Update Plan</button>
                                </div>
                            </form>
                        </div>

                        <!-- Notifications Tab -->
                        <div x-show="settingsTab === 'notifications'" class="space-y-4 animate-in fade-in duration-300">
                            @foreach(['Email Notifications', 'Browser Push', 'SMS Alerts', 'Marketing Inquiries'] as $n)
                            <div class="flex items-center justify-between p-4 border-b border-gray-50">
                                <span class="text-sm font-bold text-gray-700">{{ $n }}</span>
                                <div class="w-10 h-5 bg-primary rounded-full relative"><div class="absolute right-1 top-1 w-3 h-3 bg-white rounded-full"></div></div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Security Tab -->
                        <div x-show="settingsTab === 'security'" class="space-y-4 animate-in fade-in duration-300">
                            <div class="space-y-1"><label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Current Password</label><input type="password" class="w-full px-4 py-2.5 border border-gray-100 bg-gray-50 rounded-lg outline-none text-sm font-medium"></div>
                            <div class="space-y-1"><label class="text-[10px] font-black uppercase tracking-widest text-gray-400">New Password</label><input type="password" class="w-full px-4 py-2.5 border border-gray-100 bg-gray-50 rounded-lg outline-none text-sm font-medium"></div>
                            <div class="flex items-center space-x-3 p-4 bg-amber-50 rounded-lg text-amber-700 border border-amber-100">
                                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                                <p class="text-xs font-bold">Two-factor authentication is currently disabled.</p>
                            </div>
                        </div>
                    </div>
                    <div class="pt-6 border-t border-gray-50 flex justify-end">
                        <button class="px-6 py-2 bg-gray-900 text-white rounded-md font-medium text-xs hover:bg-black transition-all">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- VIEW USERS MODAL -->
    <div x-show="viewUsersOpen" class="fixed inset-0 z-[100] flex items-center justify-center" style="display: none;">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="viewUsersOpen = false"></div>
        <div class="bg-white w-full max-w-2xl rounded-lg shadow-2xl relative z-10 p-8 border border-gray-100 animate-in zoom-in duration-300">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-medium">Team Members</h2>
                <button @click="viewUsersOpen = false" class="text-gray-400 hover:text-gray-900"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <div class="overflow-hidden border border-gray-100 rounded-lg">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-[10px] uppercase tracking-widest text-gray-400"><tr class="divide-x divide-gray-100"><th class="px-6 py-3 font-medium">Name</th><th class="px-6 py-3 font-medium">Role</th><th class="px-6 py-3 font-medium">Status</th><th class="px-6 py-3 font-medium text-right">Actions</th></tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr class="hover:bg-gray-50 transition-all"><td class="px-6 py-4 font-medium">{{ Auth::user()->name }}</td><td class="px-6 py-4 capitalize">{{ Auth::user()->role }} (Owner)</td><td class="px-6 py-4"><span class="px-2 py-0.5 bg-green-50 text-green-600 rounded-full text-[10px] font-bold">Active</span></td><td class="px-6 py-4 text-right"><button class="text-primary font-medium">Edit</button></td></tr>
                        @if(isset($teamMembers))
                            @foreach($teamMembers as $member)
                            <tr class="hover:bg-gray-50 transition-all"><td class="px-6 py-4 font-medium">{{ $member->user->name ?? 'Invited' }}</td><td class="px-6 py-4 capitalize">{{ $member->role }}</td><td class="px-6 py-4"><span class="px-2 py-0.5 bg-green-50 text-green-600 rounded-full text-[10px] font-bold">Active</span></td><td class="px-6 py-4 text-right"><button class="text-primary font-medium">Edit</button></td></tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ADD USER MODAL -->
    <div x-show="addUserOpen" class="fixed inset-0 z-[100] flex items-center justify-center" style="display: none;">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="addUserOpen = false"></div>
        <div class="bg-white w-full max-w-md rounded-lg shadow-2xl relative z-10 p-8 border border-gray-100 animate-in zoom-in duration-300">
            <h2 class="text-2xl font-medium mb-6">Invite Team Member</h2>
            <form class="space-y-4">
                <div><label class="text-xs font-medium text-gray-400">Full Name</label><input type="text" class="w-full px-4 py-2 border border-gray-200 rounded-md outline-none text-sm"></div>
                <div><label class="text-xs font-medium text-gray-400">Email</label><input type="email" class="w-full px-4 py-2 border border-gray-200 rounded-md outline-none text-sm"></div>
                <button type="button" @click="addUserOpen = false" class="w-full py-3 bg-primary text-white rounded-md font-medium text-sm">Send Invitation</button>
            </form>
        </div>
    </div>

    <!-- ADD SERVICE MODAL -->
    @if(Auth::user()->role === 'provider')
    <div x-show="addServiceOpen" class="fixed inset-0 z-[100] flex items-center justify-center" style="display: none;">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-md" @click="addServiceOpen = false"></div>
        <div class="bg-white w-full max-w-lg rounded-lg shadow-2xl relative z-10 p-10 border border-gray-100 animate-in zoom-in duration-300">
            <div class="flex items-center justify-between mb-8"><h2 class="text-2xl font-medium">Add Service</h2><button @click="addServiceOpen = false" class="text-gray-400 hover:text-gray-600"><i data-lucide="x" class="w-6 h-6"></i></button></div>
            <form action="{{ route('provider.services.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="space-y-1"><label class="text-[10px] font-medium uppercase tracking-widest text-gray-400">Service Catalog</label><select name="service_id" class="w-full px-4 py-2.5 border border-gray-100 bg-gray-50 rounded-md text-sm font-medium">@foreach(\App\Models\Service::all() as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select></div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1"><label class="text-[10px] font-medium uppercase tracking-widest text-gray-400">Price (SAR)</label><input type="number" name="price" step="0.01" class="w-full px-4 py-2.5 border border-gray-100 rounded-md text-sm"></div>
                    <div class="space-y-1"><label class="text-[10px] font-medium uppercase tracking-widest text-gray-400">Days</label><input type="number" name="delivery_time_days" class="w-full px-4 py-2.5 border border-gray-100 rounded-md text-sm"></div>
                </div>
                <button type="submit" class="w-full py-4 bg-primary text-white rounded-md font-medium">Add to Portfolio</button>
            </form>
        </div>
    </div>
    @endif

    <script>lucide.createIcons();</script>
</body>
</html>
