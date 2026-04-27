@extends('layouts.app')

@section('content')
<div class="w-full max-w-7xl mx-auto space-y-10 animate-in fade-in duration-700">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight">Portfolio</h1>
            <p class="text-gray-500 font-medium mt-1 text-lg">Manage your companies and business entities.</p>
        </div>
        <button class="px-6 py-3 bg-primary text-white rounded-lg font-bold text-sm hover:bg-primary-dark transition-all flex items-center space-x-2 shadow-lg shadow-primary/20">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Add Company</span>
        </button>
    </div>

    <!-- Company Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div class="bg-white border border-gray-100 p-8 rounded-lg shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-start justify-between mb-8">
                <div class="w-16 h-16 bg-gray-900 rounded-lg flex items-center justify-center text-white font-black text-xl">RC</div>
                <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-blue-100">Primary</span>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Retail Corp</h3>
            <p class="text-sm text-gray-400 font-medium mb-8">Registered in Riyadh • 12 Employees</p>
            <div class="pt-6 border-t border-gray-50 flex items-center justify-between">
                <div class="flex -space-x-2">
                    <div class="w-6 h-6 rounded-full border-2 border-white bg-primary"></div>
                    <div class="w-6 h-6 rounded-full border-2 border-white bg-gray-200"></div>
                </div>
                <button class="text-primary font-bold text-xs uppercase tracking-widest hover:underline">Manage Settings</button>
            </div>
        </div>

        <div class="bg-white border border-gray-100 p-8 rounded-lg shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-start justify-between mb-8">
                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500 font-black text-xl">TV</div>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Tech Ventures</h3>
            <p class="text-sm text-gray-400 font-medium mb-8">Registered in Jeddah • 4 Employees</p>
            <div class="pt-6 border-t border-gray-50 flex items-center justify-between">
                <div class="flex -space-x-2">
                    <div class="w-6 h-6 rounded-full border-2 border-white bg-gray-200"></div>
                </div>
                <button class="text-primary font-bold text-xs uppercase tracking-widest hover:underline">Manage Settings</button>
            </div>
        </div>

        <!-- Add New Placeholder -->
        <div class="border-2 border-dashed border-gray-100 rounded-lg p-8 flex flex-col items-center justify-center text-center space-y-4 opacity-60 hover:opacity-100 transition-all cursor-pointer">
            <div class="w-12 h-12 bg-gray-50 rounded-lg flex items-center justify-center text-gray-300">
                <i data-lucide="plus" class="w-6 h-6"></i>
            </div>
            <p class="text-sm font-bold text-gray-400">Add another business entity</p>
        </div>
    </div>
</div>
@endsection
