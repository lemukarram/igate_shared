@extends('layouts.app')

@section('content')
<div class="max-w-6xl w-full space-y-8 p-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">My Service Portfolio</h1>
            <p class="text-gray-500">Manage the services you offer on iGate Shared Services.</p>
        </div>
        <button onclick="document.getElementById('addServiceModal').classList.remove('hidden')" 
                class="px-6 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-100 flex items-center space-x-2">
            <i data-lucide="plus" class="w-5 h-5"></i>
            <span>Add Service</span>
        </button>
    </div>

    <!-- Active Services Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($myServices as $ps)
        <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all group relative">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600">
                    <i data-lucide="{{ $ps->service->icon }}" class="w-6 h-6"></i>
                </div>
                <form action="{{ route('provider.services.destroy', $ps->id) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-gray-300 hover:text-red-500 transition-colors">
                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                    </button>
                </form>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $ps->service->name }}</h3>
            <p class="text-sm text-gray-500 mb-4">{{ Str::limit($ps->service->description, 60) }}</p>
            
            <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                <div>
                    <span class="block text-xs text-gray-400 uppercase font-semibold">Price</span>
                    <span class="text-xl font-bold text-gray-900">{{ number_format($ps->price, 2) }} SAR</span>
                </div>
                <div class="text-right">
                    <span class="block text-xs text-gray-400 uppercase font-semibold">Delivery</span>
                    <span class="text-sm font-bold text-gray-900">{{ $ps->delivery_time_days }} Days</span>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 text-center">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="briefcase" class="w-10 h-10 text-gray-300"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">No services added yet</h3>
            <p class="text-gray-500 max-w-xs mx-auto">Click "Add Service" to start offering your professional services to clients.</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Simple Modal (Hidden by default) -->
<div id="addServiceModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-white w-full max-w-lg rounded-3xl p-8 shadow-2xl animate-in zoom-in duration-200">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold">Add Service to Portfolio</h2>
            <button onclick="document.getElementById('addServiceModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <form action="{{ route('provider.services.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Select iGate Shared Services Standard Service</label>
                <select name="service_id" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all">
                    @foreach($allServices as $s)
                    <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->category }})</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-400 italic">* Scope is fixed per iGate Shared Services standards.</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Price (SAR)</label>
                    <input type="number" name="price" step="0.01" required placeholder="0.00"
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">SLA (Days)</label>
                    <input type="number" name="delivery_time_days" required placeholder="e.g. 7"
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Provider Notes (Optional)</label>
                <textarea name="provider_notes" rows="3" placeholder="Additional details about your specific approach..."
                          class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all"></textarea>
            </div>

            <button type="submit" class="w-full py-4 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">
                Add to Portfolio
            </button>
        </form>
    </div>
</div>
@endsection
