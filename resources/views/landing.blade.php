<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iGate Shared Services - Standardized B2B Service Marketplace in KSA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @font-face {
            font-family: 'Poppins';
            src: url('/fonts/Poppins/Poppins-Thin.ttf') format('truetype');
            font-weight: 100;
        }
        @font-face {
            font-family: 'Poppins';
            src: url('/fonts/Poppins/Poppins-Light.ttf') format('truetype');
            font-weight: 300;
        }
        @font-face {
            font-family: 'Poppins';
            src: url('/fonts/Poppins/Poppins-Regular.ttf') format('truetype');
            font-weight: 400;
        }
        @font-face {
            font-family: 'Poppins';
            src: url('/fonts/Poppins/Poppins-Bold.ttf') format('truetype');
            font-weight: 700;
        }
        body { font-family: 'Poppins', sans-serif; }
        .hero-gradient { background: radial-gradient(circle at top right, #eff6ff 0%, #ffffff 50%); }
        .animate-float { animation: float 6s ease-in-out infinite; }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body class="bg-white text-gray-900 overflow-x-hidden selection:bg-blue-100 selection:text-blue-900">
    <!-- Navbar -->
    <nav class="fixed w-full z-50 bg-white/60 backdrop-blur-xl border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 h-24 flex items-center justify-between">
            <div class="flex items-center">
                <img src="/images/logo/logo.png" alt="iGate Shared Services" class="h-12 object-contain">
            </div>
            
            <div class="hidden lg:flex items-center space-x-12 text-sm font-bold text-gray-500 uppercase tracking-widest">
                <a href="#about" class="hover:text-blue-600 transition-all">About</a>
                <a href="#services" class="hover:text-blue-600 transition-all">Services</a>
                <a href="#providers" class="hover:text-blue-600 transition-all">Providers</a>
                <a href="/terms" class="hover:text-blue-600 transition-all">Terms</a>
            </div>

            <div class="flex items-center space-x-6">
                <a href="/login" class="text-sm font-bold text-gray-900 hover:text-blue-600 transition-colors">Login</a>
                <a href="/login" class="bg-gray-900 text-white px-8 py-4 rounded-2xl font-bold text-sm hover:bg-blue-600 transition-all shadow-xl shadow-gray-200 hover:shadow-blue-200 active:scale-95">Get Started</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-52 pb-32 px-6 hero-gradient overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-blue-50 rounded-full blur-3xl -mr-96 -mt-96 opacity-50"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-indigo-50 rounded-full blur-3xl -ml-64 -mb-64 opacity-50"></div>

        <div class="max-w-7xl mx-auto text-center relative z-10">
            <div class="inline-flex items-center space-x-3 bg-white/80 border border-blue-100 text-blue-600 px-6 py-2.5 rounded-full text-[10px] font-black uppercase tracking-[0.2em] mb-12 shadow-sm animate-in fade-in slide-in-from-top-4 duration-1000">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
                </span>
                <span>The Operating System for Business in KSA</span>
            </div>
            
            <h1 class="text-7xl md:text-[7rem] font-black text-gray-900 leading-[0.9] tracking-tighter mb-12 animate-in fade-in slide-in-from-bottom-8 duration-1000 delay-100">
                Outsource with <br> <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Absolute Trust.</span>
            </h1>
            
            <p class="text-xl text-gray-500 max-w-2xl mx-auto leading-relaxed mb-16 font-medium animate-in fade-in slide-in-from-bottom-8 duration-1000 delay-200">
                Saudi Arabia's first strictly standardized B2B marketplace. Fixed scope, verified agencies, and secure escrow protection.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-6 pt-4 animate-in fade-in slide-in-from-bottom-8 duration-1000 delay-300">
                <a href="/login" class="group w-full sm:w-auto px-12 py-6 bg-blue-600 text-white rounded-[2rem] font-black text-lg hover:bg-blue-700 transition-all shadow-2xl shadow-blue-200 hover:-translate-y-1 flex items-center justify-center space-x-3">
                    <span>Join as Client</span>
                    <i data-lucide="arrow-right" class="w-6 h-6 group-hover:translate-x-1 transition-transform"></i>
                </a>
                <a href="/login" class="w-full sm:w-auto px-12 py-6 bg-white text-gray-900 border-2 border-gray-100 rounded-[2rem] font-black text-lg hover:bg-gray-50 transition-all flex items-center justify-center space-x-3">
                    <span>Join as Provider</span>
                </a>
            </div>

            <!-- Dashboard Preview Graphic -->
            <div class="mt-32 relative max-w-5xl mx-auto animate-in fade-in slide-in-from-bottom-12 duration-1000 delay-500">
                <div class="bg-white rounded-[3rem] shadow-[0_50px_100px_-20px_rgba(0,0,0,0.1)] border border-gray-100 overflow-hidden p-4">
                    <div class="bg-gray-50 rounded-[2.5rem] h-[500px] w-full flex items-center justify-center overflow-hidden">
                        <img src="/images/logo/logo.png" class="w-64 opacity-10 animate-float" alt="">
                    </div>
                </div>
                <!-- Floaters -->
                <div class="absolute -top-10 -right-10 bg-white p-6 rounded-[2rem] shadow-xl border border-gray-50 hidden md:block animate-float">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center text-green-600">
                            <i data-lucide="check-circle" class="w-6 h-6"></i>
                        </div>
                        <div class="text-left">
                            <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Payment Released</p>
                            <p class="text-lg font-bold">24,500 SAR</p>
                        </div>
                    </div>
                </div>
                <div class="absolute bottom-20 -left-10 bg-white p-6 rounded-[2rem] shadow-xl border border-gray-50 hidden md:block animate-float" style="animation-delay: -3s">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600">
                            <i data-lucide="shield-check" class="w-6 h-6"></i>
                        </div>
                        <div class="text-left">
                            <p class="text-xs font-black text-gray-400 uppercase tracking-widest">SLA Verified</p>
                            <p class="text-lg font-bold">100% Success</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Partners / Proof -->
    <section class="py-24 border-y border-gray-50 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <p class="text-center text-xs font-black text-gray-400 uppercase tracking-[0.3em] mb-12">Verified B2B Service Verticals</p>
            <div class="flex flex-wrap justify-center gap-12 lg:gap-24 opacity-40 grayscale contrast-200">
                <span class="text-2xl font-black italic">FINANCE</span>
                <span class="text-2xl font-black italic">LEGAL</span>
                <span class="text-2xl font-black italic">TECH</span>
                <span class="text-2xl font-black italic">OPERATIONS</span>
                <span class="text-2xl font-black italic">MARKETING</span>
            </div>
        </div>
    </section>

    <!-- Services Grid -->
    <section id="services" class="py-32 bg-gray-50/30 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-24 space-y-4">
                <h2 class="text-5xl font-black tracking-tight text-gray-900">One Catalog. Infinite Growth.</h2>
                <p class="text-gray-500 text-lg max-w-2xl mx-auto">We've defined the standard for the 12 most critical business services in the Kingdom.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="group bg-white p-10 rounded-[3rem] border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 mb-8 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 shadow-lg shadow-blue-100">
                        <i data-lucide="landmark" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-2xl font-black mb-4">ZATCA & VAT</h3>
                    <p class="text-gray-500 leading-relaxed font-medium">Full Fatoora Phase 2 integration and recurring VAT management for Saudi SMEs.</p>
                </div>
                <div class="group bg-white p-10 rounded-[3rem] border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                    <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 mb-8 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-500 shadow-lg shadow-indigo-100">
                        <i data-lucide="users-2" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-2xl font-black mb-4">Human Capital</h3>
                    <p class="text-gray-500 leading-relaxed font-medium">Standardized payroll, GOSI, and Qiwa management with verified HR partners.</p>
                </div>
                <div class="group bg-white p-10 rounded-[3rem] border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                    <div class="w-16 h-16 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-600 mb-8 group-hover:bg-purple-600 group-hover:text-white transition-all duration-500 shadow-lg shadow-purple-100">
                        <i data-lucide="scale" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-2xl font-black mb-4">Legal Advisory</h3>
                    <p class="text-gray-500 leading-relaxed font-medium">Commercial contract reviews and IP protection by KSA-licensed attorneys.</p>
                </div>
            </div>
            
            <div class="mt-20 text-center">
                <a href="/login" class="inline-flex items-center space-x-3 text-lg font-black text-gray-900 group">
                    <span>Explore All 12 Services</span>
                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center group-hover:bg-gray-900 group-hover:text-white transition-all">
                        <i data-lucide="chevron-right" class="w-5 h-5"></i>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-32 px-6">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-20">
            <div class="col-span-1 md:col-span-1 space-y-10">
                <div class="flex items-center">
                    <img src="/images/logo/logo.png" class="h-10 object-contain" alt="iGate Shared Services">
                </div>
                <p class="text-gray-400 font-medium leading-relaxed">The Operating System for B2B transactions in the Kingdom of Saudi Arabia.</p>
                <div class="flex space-x-4">
                    <div class="w-10 h-10 bg-gray-50 rounded-xl"></div>
                    <div class="w-10 h-10 bg-gray-50 rounded-xl"></div>
                    <div class="w-10 h-10 bg-gray-50 rounded-xl"></div>
                </div>
            </div>
            <div>
                <h4 class="font-black text-gray-900 mb-8 text-sm uppercase tracking-widest">Marketplace</h4>
                <ul class="space-y-6 text-sm text-gray-500 font-bold">
                    <li><a href="/login" class="hover:text-blue-600 transition-all">Service Catalog</a></li>
                    <li><a href="/login" class="hover:text-blue-600 transition-all">Verified Providers</a></li>
                    <li><a href="/login" class="hover:text-blue-600 transition-all">Enterprise Solutions</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-black text-gray-900 mb-8 text-sm uppercase tracking-widest">Governance</h4>
                <ul class="space-y-6 text-sm text-gray-500 font-bold">
                    <li><a href="#" class="hover:text-blue-600 transition-all">iGate Shared Services Escrow Policy</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition-all">Terms & Conditions</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition-all">SLA Framework</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-black text-gray-900 mb-8 text-sm uppercase tracking-widest">Support</h4>
                <ul class="space-y-6 text-sm text-gray-500 font-bold">
                    <li>support@igate.com</li>
                    <li>Help Center</li>
                    <li>API Documentation</li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto pt-20 mt-20 border-t border-gray-50 text-center text-gray-400 text-[10px] font-black uppercase tracking-[0.4em]">
            © 2026 iGate Shared Services • Riyadh, KSA
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
