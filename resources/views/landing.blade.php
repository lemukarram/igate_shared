<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iGate Shared Services - Standardized B2B Service Marketplace in KSA.</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');
        body { font-family: 'Poppins', sans-serif; }
        
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
    </style>
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden selection:bg-[#3da9e4] selection:text-white">
    <!-- Navbar -->
    <nav class="fixed w-full z-50 glass-card">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center">
                <img src="/images/logo/logo.png" alt="iGate Shared Services" class="h-10 object-contain">
            </div>
            
            <div class="hidden lg:flex items-center space-x-10 text-sm font-medium text-gray-600">
                <a href="#about" class="hover:theme-text transition-all fancy-border">About</a>
                <a href="#services" class="hover:theme-text transition-all fancy-border">Services</a>
                <a href="#why-us" class="hover:theme-text transition-all fancy-border">Why iGate</a>
                <a href="/terms" class="hover:theme-text transition-all fancy-border">Terms</a>
            </div>

            <div class="flex items-center space-x-4">
                <a href="/login" class="text-sm font-medium text-gray-700 hover:theme-text transition-colors">Login</a>
                <a href="/login" class="theme-bg text-white px-6 py-2.5 rounded-lg font-medium text-sm theme-hover-bg transition-all shadow-md active:scale-95">Get Started</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-40 pb-20 px-6 hero-gradient overflow-hidden">
        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center justify-between relative z-10 gap-12">
            <div class="w-full lg:w-1/2 text-left">
                <div class="inline-flex items-center space-x-2 bg-white border border-[#3da9e4] theme-text px-4 py-1.5 rounded-md text-xs font-medium uppercase tracking-wider mb-6 shadow-sm">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#3da9e4] opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-[#3da9e4]"></span>
                    </span>
                    <span>The Operating System for Business in KSA</span>
                </div>
                
                <h1 class="text-5xl lg:text-6xl font-semibold text-gray-900 leading-tight mb-6">
                    Outsource with <br> <span class="theme-text">Absolute Trust.</span>
                </h1>
                
                <p class="text-lg text-gray-600 leading-relaxed mb-10 font-normal">
                    Saudi Arabia's first strictly standardized B2B marketplace. Fixed scope, verified agencies, and secure escrow protection.
                </p>
                
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <a href="/login" class="w-full sm:w-auto px-8 py-3.5 theme-bg text-white rounded-lg font-medium hover:bg-[#2b8bc2] transition-all shadow-lg flex items-center justify-center space-x-2">
                        <span>Join as Client</span>
                        <i data-lucide="arrow-right" class="w-5 h-5"></i>
                    </a>
                    <a href="/login" class="w-full sm:w-auto px-8 py-3.5 bg-white text-gray-800 border border-gray-200 rounded-lg font-medium hover:bg-gray-50 transition-all flex items-center justify-center space-x-2 shadow-sm">
                        <span>Join as Provider</span>
                    </a>
                </div>
            </div>

            <!-- Dashboard Preview Graphic -->
            <div class="w-full lg:w-1/2 relative animate-float">
                <div class="glass-card rounded-lg p-3 shadow-xl relative z-10 border-t border-l border-white/60">
                    <img src="/images/igate-banner.jpg" class="w-full rounded-md " alt="Dashboard Preview" style="min-height: 300px; object-fit: contain; background: #fafafa;">
                    
                    <div class="absolute -left-6 top-10 bg-white p-4 rounded-lg shadow-lg border border-gray-100 flex items-center space-x-3">
                        <div class="w-10 h-10 bg-[#e6f4fd] rounded-md flex items-center justify-center theme-text">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Payment Released</p>
                            <p class="text-sm font-semibold">24,500 SAR</p>
                        </div>
                    </div>

                    <div class="absolute -right-6 bottom-10 bg-white p-4 rounded-lg shadow-lg border border-gray-100 flex items-center space-x-3">
                        <div class="w-10 h-10 bg-[#e6f4fd] rounded-md flex items-center justify-center theme-text">
                            <i data-lucide="shield-check" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">SLA Verified</p>
                            <p class="text-sm font-semibold">100% Success</p>
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
                <h2 class="text-3xl lg:text-4xl font-semibold text-gray-900 mb-4">Why iGate Shared Services?</h2>
                <p class="text-gray-500 font-medium max-w-2xl mx-auto">A robust platform designed to eliminate the friction in B2B transactions through standardization and transparency.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Card 1 -->
                <div class="p-8 rounded-lg border border-gray-100 bg-gray-50 hover:bg-white hover:shadow-xl transition-all duration-300 group cursor-pointer">
                    <div class="w-14 h-14 bg-[#e6f4fd] rounded-lg flex items-center justify-center theme-text mb-6 group-hover:bg-[#3da9e4] group-hover:text-white transition-colors">
                        <i data-lucide="lock" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-800">Escrow Security</h3>
                    <p class="text-sm text-gray-500 leading-relaxed font-normal">Funds are securely held in escrow and released only upon verified milestone completion, ensuring trust on both sides.</p>
                </div>
                <!-- Card 2 -->
                <div class="p-8 rounded-lg border border-gray-100 bg-gray-50 hover:bg-white hover:shadow-xl transition-all duration-300 group cursor-pointer mt-0 lg:mt-8">
                    <div class="w-14 h-14 bg-[#e6f4fd] rounded-lg flex items-center justify-center theme-text mb-6 group-hover:bg-[#3da9e4] group-hover:text-white transition-colors">
                        <i data-lucide="layout-grid" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-800">Fixed Scopes</h3>
                    <p class="text-sm text-gray-500 leading-relaxed font-normal">12 strictly standardized service types prevent scope creep and guarantee you get exactly what you pay for.</p>
                </div>
                <!-- Card 3 -->
                <div class="p-8 rounded-lg border border-gray-100 bg-gray-50 hover:bg-white hover:shadow-xl transition-all duration-300 group cursor-pointer">
                    <div class="w-14 h-14 bg-[#e6f4fd] rounded-lg flex items-center justify-center theme-text mb-6 group-hover:bg-[#3da9e4] group-hover:text-white transition-colors">
                        <i data-lucide="check-square" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-800">Verified Providers</h3>
                    <p class="text-sm text-gray-500 leading-relaxed font-normal">Every agency is thoroughly vetted, KSA-registered, and rated by real clients for consistent quality.</p>
                </div>
                <!-- Card 4 -->
                <div class="p-8 rounded-lg border border-gray-100 bg-gray-50 hover:bg-white hover:shadow-xl transition-all duration-300 group cursor-pointer mt-0 lg:mt-8">
                    <div class="w-14 h-14 bg-[#e6f4fd] rounded-lg flex items-center justify-center theme-text mb-6 group-hover:bg-[#3da9e4] group-hover:text-white transition-colors">
                        <i data-lucide="activity" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-800">SLA Tracking</h3>
                    <p class="text-sm text-gray-500 leading-relaxed font-normal">Automated performance monitoring and centralized task boards keep projects perfectly on schedule.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Grid -->
    <section id="services" class="py-24 bg-gray-50 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12 border-b border-gray-200 pb-6">
                <div class="max-w-2xl">
                    <h2 class="text-3xl font-semibold text-gray-900 mb-3">Core Services Catalog</h2>
                    <p class="text-gray-500 font-normal">Explore our specialized catalog covering essential B2B needs.</p>
                </div>
                <a href="/login" class="theme-text font-medium flex items-center space-x-2 hover:text-[#2b8bc2] transition-colors mt-4 md:mt-0">
                    <span>View All Services</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-lg border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-[#e6f4fd] rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                    <div class="w-12 h-12 bg-[#e6f4fd] rounded-md flex items-center justify-center theme-text mb-6 relative z-10 group-hover:bg-[#3da9e4] group-hover:text-white transition-colors">
                        <i data-lucide="landmark" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-800">ZATCA & VAT</h3>
                    <p class="text-sm text-gray-500 leading-relaxed font-normal mb-6">Full Fatoora Phase 2 integration and recurring VAT management for Saudi SMEs.</p>
                    <a href="/login" class="text-sm theme-text font-medium flex items-center space-x-1 group-hover:underline">
                        <span>Learn more</span>
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </a>
                </div>
                <div class="bg-white p-8 rounded-lg border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-[#e6f4fd] rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                    <div class="w-12 h-12 bg-[#e6f4fd] rounded-md flex items-center justify-center theme-text mb-6 relative z-10 group-hover:bg-[#3da9e4] group-hover:text-white transition-colors">
                        <i data-lucide="users" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-800">Human Capital</h3>
                    <p class="text-sm text-gray-500 leading-relaxed font-normal mb-6">Standardized payroll, GOSI, and Qiwa management with verified HR partners.</p>
                    <a href="/login" class="text-sm theme-text font-medium flex items-center space-x-1 group-hover:underline">
                        <span>Learn more</span>
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </a>
                </div>
                <div class="bg-white p-8 rounded-lg border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-[#e6f4fd] rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                    <div class="w-12 h-12 bg-[#e6f4fd] rounded-md flex items-center justify-center theme-text mb-6 relative z-10 group-hover:bg-[#3da9e4] group-hover:text-white transition-colors">
                        <i data-lucide="scale" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-800">Legal Advisory</h3>
                    <p class="text-sm text-gray-500 leading-relaxed font-normal mb-6">Commercial contract reviews and IP protection by KSA-licensed attorneys.</p>
                    <a href="/login" class="text-sm theme-text font-medium flex items-center space-x-1 group-hover:underline">
                        <span>Learn more</span>
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-16 px-6">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-12">
            <div class="col-span-1 md:col-span-1 space-y-6">
                <img src="/images/logo/logo.png" class="h-8 object-contain" alt="iGate Shared Services">
                <p class="text-gray-500 font-normal text-sm leading-relaxed">The Operating System for B2B transactions in the Kingdom of Saudi Arabia.</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800 mb-6 text-sm uppercase tracking-wide">Marketplace</h4>
                <ul class="space-y-4 text-sm text-gray-500 font-medium">
                    <li><a href="/login" class="hover:theme-text transition-colors">Service Catalog</a></li>
                    <li><a href="/login" class="hover:theme-text transition-colors">Verified Providers</a></li>
                    <li><a href="/login" class="hover:theme-text transition-colors">Enterprise Solutions</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800 mb-6 text-sm uppercase tracking-wide">Governance</h4>
                <ul class="space-y-4 text-sm text-gray-500 font-medium">
                    <li><a href="/terms" class="hover:theme-text transition-colors">Escrow Policy</a></li>
                    <li><a href="/terms" class="hover:theme-text transition-colors">Terms & Conditions</a></li>
                    <li><a href="/terms" class="hover:theme-text transition-colors">SLA Framework</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800 mb-6 text-sm uppercase tracking-wide">Support</h4>
                <ul class="space-y-4 text-sm text-gray-500 font-medium">
                    <li><a href="mailto:support@igate.com" class="hover:theme-text transition-colors">support@igate.com</a></li>
                    <li><a href="#" class="hover:theme-text transition-colors">Help Center</a></li>
                    <li><a href="#" class="hover:theme-text transition-colors">API Documentation</a></li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto pt-8 mt-12 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center text-gray-400 text-xs font-medium">
            <span>© 2026 iGate Shared Services • Riyadh, KSA</span>
            <div class="flex space-x-4 mt-4 md:mt-0">
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