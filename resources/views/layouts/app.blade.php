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
                        <template x-for="t in ['account', 'settings', 'permissions', 'plans', 'notifications', 'security']">
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
                        <div x-show="settingsTab === 'account'" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1"><label class="text-xs font-medium text-gray-400">First Name</label><input type="text" value="{{ explode(' ', Auth::user()->name)[0] }}" class="w-full px-4 py-2 border border-gray-200 rounded-md outline-none focus:ring-2 focus:ring-primary/20 text-sm"></div>
                                <div class="space-y-1"><label class="text-xs font-medium text-gray-400">Last Name</label><input type="text" value="{{ explode(' ', Auth::user()->name)[1] ?? '' }}" class="w-full px-4 py-2 border border-gray-200 rounded-md outline-none focus:ring-2 focus:ring-primary/20 text-sm"></div>
                            </div>
                            <div class="space-y-1"><label class="text-xs font-medium text-gray-400">Email Address</label><input type="email" value="{{ Auth::user()->email }}" class="w-full px-4 py-2 border border-gray-200 rounded-md outline-none focus:ring-2 focus:ring-primary/20 text-sm"></div>
                        </div>
                        <div x-show="settingsTab === 'plans'" class="space-y-4">
                            <div class="p-6 border border-primary/20 bg-primary-light rounded-lg">
                                <p class="text-xs font-black text-primary uppercase mb-2">Current Plan</p>
                                <h3 class="text-xl font-bold mb-4">Enterprise Pro</h3>
                                <button class="px-6 py-2 bg-primary text-white text-xs font-bold rounded-md">Upgrade Plan</button>
                            </div>
                        </div>
                        <div x-show="!['account', 'plans'].includes(settingsTab)" class="py-20 text-center opacity-50">
                            <i data-lucide="construction" class="w-10 h-10 mx-auto mb-4"></i><p class="text-sm font-medium">Options coming soon.</p>
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
                        <tr class="hover:bg-gray-50 transition-all"><td class="px-6 py-4 font-medium">{{ Auth::user()->name }}</td><td class="px-6 py-4 capitalize">{{ Auth::user()->role }}</td><td class="px-6 py-4"><span class="px-2 py-0.5 bg-green-50 text-green-600 rounded-full text-[10px] font-bold">Active</span></td><td class="px-6 py-4 text-right"><button class="text-primary font-medium">Edit</button></td></tr>
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
