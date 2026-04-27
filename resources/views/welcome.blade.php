@extends('layouts.app')

@section('content')
<div class="text-center space-y-8 animate-in fade-in duration-700">
    <h1 class="text-4xl font-medium tracking-tight text-gray-900">
        Hi Mukarram, How can I help you?
    </h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 w-full max-w-5xl px-4">
        <!-- Quick Actions / Suggestions -->
        <div class="p-4 border border-gray-100 rounded-2xl hover:border-blue-200 hover:shadow-md transition-all cursor-pointer group bg-white text-left">
            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 mb-3 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                <i data-lucide="shield-check" class="w-5 h-5"></i>
            </div>
            <h3 class="font-semibold text-sm mb-1">ZATCA Compliance</h3>
            <p class="text-xs text-gray-500">Ensure your business meets KSA tax regulations.</p>
        </div>

        <div class="p-4 border border-gray-100 rounded-2xl hover:border-blue-200 hover:shadow-md transition-all cursor-pointer group bg-white text-left">
            <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600 mb-3 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
            <h3 class="font-semibold text-sm mb-1">HR Management</h3>
            <p class="text-xs text-gray-500">Standardized HR services for your team.</p>
        </div>

        <div class="p-4 border border-gray-100 rounded-2xl hover:border-blue-200 hover:shadow-md transition-all cursor-pointer group bg-white text-left">
            <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center text-purple-600 mb-3 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                <i data-lucide="scale" class="w-5 h-5"></i>
            </div>
            <h3 class="font-semibold text-sm mb-1">Legal Review</h3>
            <p class="text-xs text-gray-500">Professional contract and legal document review.</p>
        </div>

        <div class="p-4 border border-gray-100 rounded-2xl hover:border-blue-200 hover:shadow-md transition-all cursor-pointer group bg-white text-left">
            <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center text-emerald-600 mb-3 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                <i data-lucide="trending-up" class="w-5 h-5"></i>
            </div>
            <h3 class="font-semibold text-sm mb-1">SEO & Digital</h3>
            <p class="text-xs text-gray-500">Boost your business visibility in KSA.</p>
        </div>
    </div>
</div>
@endsection
