@extends('layouts.app')

@section('content')
<div class="w-full max-w-7xl mx-auto space-y-10 animate-in fade-in duration-700 relative" x-data="{ addCompanyModal: false }">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-gray-900 tracking-tight">Portfolio</h1>
            <p class="text-gray-500 font-medium mt-1 text-sm">Manage your companies and business entities.</p>
        </div>
        <button @click="addCompanyModal = true" class="px-5 py-2.5 bg-[#3da9e4] text-white rounded-lg font-medium text-sm hover:bg-[#2b8bc2] transition-all flex items-center space-x-2 shadow-md">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Add Company</span>
        </button>
    </div>

    @if(session('success'))
        <div class="bg-[#e6f4fd] text-[#3da9e4] p-4 rounded-lg text-sm font-medium border border-[#3da9e4]/30">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 text-red-500 p-4 rounded-lg text-sm font-medium border border-red-100">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Company Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($companies as $index => $company)
        <a href="{{ route('companies.show', $company->id) }}" class="block bg-white border border-gray-100 p-6 rounded-lg shadow-sm hover:shadow-md hover:border-[#3da9e4]/50 transition-all group relative">
            <div class="flex items-start justify-between mb-6">
                <div class="w-14 h-14 bg-gray-50 rounded-lg flex items-center justify-center text-gray-600 font-semibold text-xl border border-gray-100">
                    {{ substr($company->name, 0, 2) }}
                </div>
                @if($index === 0)
                <span class="px-2 py-0.5 bg-[#e6f4fd] text-[#3da9e4] rounded text-[10px] font-semibold uppercase tracking-wider">Primary</span>
                @endif
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-1 group-hover:text-[#3da9e4] transition-colors">{{ $company->name }}</h3>
            <p class="text-xs text-gray-500 font-medium mb-6 line-clamp-1">
                {{ $company->industry ? $company->industry . ' • ' : '' }} {{ $company->registration_number ? 'Reg: ' . $company->registration_number : 'New Entity' }}
            </p>
            <div class="pt-4 border-t border-gray-50 flex items-center justify-between">
                <div class="flex -space-x-2">
                    <div class="w-6 h-6 rounded-full border border-white bg-[#3da9e4] text-white flex items-center justify-center text-[10px] font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</div>
                </div>
                <span class="text-[#3da9e4] font-medium text-xs flex items-center space-x-1">
                    <span>Manage</span>
                    <i data-lucide="arrow-right" class="w-3 h-3 group-hover:translate-x-1 transition-transform"></i>
                </span>
            </div>
        </a>
        @endforeach

        <!-- Add New Placeholder -->
        <div @click="addCompanyModal = true" class="border-2 border-dashed border-gray-200 rounded-lg p-6 flex flex-col items-center justify-center text-center space-y-3 bg-gray-50 hover:bg-white hover:border-[#3da9e4] transition-all cursor-pointer group">
            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-gray-400 group-hover:text-[#3da9e4] group-hover:scale-110 transition-all shadow-sm">
                <i data-lucide="plus" class="w-5 h-5"></i>
            </div>
            <p class="text-sm font-semibold text-gray-500 group-hover:text-[#3da9e4]">Add another business entity</p>
        </div>
    </div>

    <!-- Add Company Modal -->
    <div x-show="addCompanyModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
        <div @click.away="addCompanyModal = false" class="bg-white rounded-lg shadow-xl w-full max-w-md border border-gray-100 overflow-hidden animate-in zoom-in duration-200">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">Add New Company</h2>
                <button @click="addCompanyModal = false" class="text-gray-400 hover:text-gray-600"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form action="{{ route('companies.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Company Name *</label>
                        <input type="text" name="name" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-[#3da9e4]/50 focus:border-[#3da9e4] outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Industry</label>
                        <input type="text" name="industry" placeholder="e.g. Retail, Tech, Construction" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-[#3da9e4]/50 focus:border-[#3da9e4] outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Commercial Registration Number</label>
                        <input type="text" name="registration_number" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-[#3da9e4]/50 focus:border-[#3da9e4] outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">About Company</label>
                        <textarea name="about" rows="3" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-[#3da9e4]/50 focus:border-[#3da9e4] outline-none text-sm resize-none"></textarea>
                    </div>
                </div>
                <div class="p-5 border-t border-gray-100 bg-gray-50 flex justify-end space-x-3">
                    <button type="button" @click="addCompanyModal = false" class="px-4 py-2 text-gray-600 bg-white border border-gray-200 rounded-md font-medium text-sm hover:bg-gray-50 transition-colors">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-[#3da9e4] text-white rounded-md font-medium text-sm hover:bg-[#2b8bc2] transition-colors">Save Company</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection