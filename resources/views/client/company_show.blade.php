@extends('layouts.app')

@section('content')
<div class="w-full max-w-7xl mx-auto space-y-6 animate-in fade-in duration-700 relative" x-data="{ editModal: false, manageUsersModal: false }">
    <!-- Header -->
    <div class="flex items-center justify-between bg-white p-6 rounded-lg border border-gray-100 shadow-sm">
        <div class="flex items-center space-x-5">
            <div class="w-16 h-16 bg-gray-50 border border-gray-200 rounded-lg flex items-center justify-center text-[#3da9e4] font-semibold text-2xl">
                {{ substr($company->name, 0, 2) }}
            </div>
            <div>
                <div class="flex items-center space-x-3 mb-1">
                    <h1 class="text-2xl font-semibold text-gray-900 tracking-tight">{{ $company->name }}</h1>
                    <span class="px-2 py-0.5 bg-[#e6f4fd] text-[#3da9e4] rounded text-[10px] font-semibold uppercase tracking-wider">Verified Entity</span>
                </div>
                <p class="text-gray-500 font-medium text-sm flex items-center space-x-4">
                    <span><i data-lucide="briefcase" class="inline w-3 h-3 mr-1"></i> {{ $company->industry ?? 'General' }}</span>
                    <span><i data-lucide="file-text" class="inline w-3 h-3 mr-1"></i> CR: {{ $company->registration_number ?? 'Not provided' }}</span>
                </p>
            </div>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('explore.index') }}" class="px-4 py-2 bg-white border border-[#3da9e4] text-[#3da9e4] rounded-lg font-medium text-sm hover:bg-[#e6f4fd] transition-colors flex items-center space-x-2">
                <i data-lucide="search" class="w-4 h-4"></i>
                <span>Request Service</span>
            </a>
            <button @click="editModal = true" class="px-4 py-2 bg-gray-50 text-gray-600 rounded-lg font-medium text-sm hover:bg-gray-100 border border-gray-200 transition-colors flex items-center space-x-2">
                <i data-lucide="edit" class="w-4 h-4"></i>
                <span>Edit Details</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-[#e6f4fd] text-[#3da9e4] p-4 rounded-lg text-sm font-medium border border-[#3da9e4]/30">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Area: Active Services -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-gray-100 rounded-lg shadow-sm overflow-hidden flex flex-col h-full">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h2 class="text-lg font-semibold text-gray-900">Active Services</h2>
                    <a href="{{ route('explore.index') }}" class="text-sm font-medium text-[#3da9e4] hover:underline">Add New</a>
                </div>
                <div class="p-5 space-y-4 flex-1">
                    @forelse($company->projects as $project)
                        <div class="flex items-center justify-between p-4 border border-gray-100 rounded-lg hover:border-[#3da9e4]/50 hover:shadow-sm transition-all group bg-white">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded bg-[#e6f4fd] text-[#3da9e4] flex items-center justify-center">
                                    <i data-lucide="{{ $project->service->icon ?? 'briefcase' }}" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 text-sm group-hover:text-[#3da9e4] transition-colors">{{ $project->service->name }}</h4>
                                    <p class="text-xs text-gray-500 font-medium">Provider: {{ $project->provider->name }}</p>
                                </div>
                            </div>
                            <div class="text-right flex items-center space-x-6">
                                <div class="hidden md:block">
                                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-1">Status</p>
                                    <span class="px-2 py-0.5 bg-green-50 text-green-600 rounded text-[10px] font-semibold">{{ ucfirst($project->status) }}</span>
                                </div>
                                <a href="{{ route('projects.show', $project->id) }}" class="p-2 text-gray-400 hover:text-[#3da9e4] bg-gray-50 hover:bg-[#e6f4fd] rounded-md transition-colors">
                                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                                <i data-lucide="layers" class="w-6 h-6"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-900">No active services</p>
                            <p class="text-xs text-gray-500 mb-4">Request a service from our marketplace to get started.</p>
                            <a href="{{ route('explore.index') }}" class="inline-block px-4 py-2 bg-[#3da9e4] text-white rounded text-xs font-medium hover:bg-[#2b8bc2] transition-colors">Explore Marketplace</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar: Company Details & Users -->
        <div class="space-y-6">
            <!-- Details Card -->
            <div class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-50">About Company</h3>
                <p class="text-sm text-gray-600 leading-relaxed mb-4">
                    {{ $company->about ?? 'No description provided.' }}
                </p>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-medium">Industry</span>
                        <span class="font-medium text-gray-900">{{ $company->industry ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-medium">Registration</span>
                        <span class="font-medium text-gray-900">{{ $company->registration_number ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-medium">Created On</span>
                        <span class="font-medium text-gray-900">{{ $company->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Users Card -->
            <div class="bg-white border border-gray-100 rounded-lg shadow-sm flex flex-col">
                <div class="p-5 border-b border-gray-50 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900">Assigned Users</h3>
                    <button @click="manageUsersModal = true" class="text-xs text-[#3da9e4] font-medium hover:underline flex items-center">
                        <i data-lucide="settings" class="w-3 h-3 mr-1"></i> Manage
                    </button>
                </div>
                <div class="p-5 space-y-4">
                    <!-- Owner -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded bg-[#3da9e4] text-white flex items-center justify-center text-xs font-semibold">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }} <span class="text-xs text-gray-400 font-normal">(You)</span></p>
                                <p class="text-[10px] text-gray-500 uppercase tracking-widest font-semibold">Owner</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mock assigned user -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded bg-gray-100 text-gray-600 flex items-center justify-center text-xs font-semibold">
                                SA
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Sarah Ahmed</p>
                                <p class="text-[10px] text-gray-500 uppercase tracking-widest font-semibold">Finance Manager</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-4 border-t border-gray-50 bg-gray-50/50 rounded-b-lg text-center">
                    <button @click="manageUsersModal = true" class="text-xs font-medium text-gray-600 hover:text-gray-900 flex items-center justify-center mx-auto space-x-1">
                        <i data-lucide="plus" class="w-3 h-3"></i>
                        <span>Invite User</span>
                    </button>
                </div>
            </div>
            
            <form action="{{ route('companies.destroy', $company->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this company? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full py-3 border border-red-200 text-red-500 bg-red-50 hover:bg-red-100 rounded-lg text-xs font-semibold transition-colors flex items-center justify-center space-x-2">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    <span>Delete Company</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Edit Company Modal -->
    <div x-show="editModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
        <div @click.away="editModal = false" class="bg-white rounded-lg shadow-xl w-full max-w-md border border-gray-100 overflow-hidden animate-in zoom-in duration-200">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">Edit Company</h2>
                <button @click="editModal = false" class="text-gray-400 hover:text-gray-600"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form action="{{ route('companies.update', $company->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Company Name *</label>
                        <input type="text" name="name" value="{{ $company->name }}" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-[#3da9e4]/50 focus:border-[#3da9e4] outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Industry</label>
                        <input type="text" name="industry" value="{{ $company->industry }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-[#3da9e4]/50 focus:border-[#3da9e4] outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Commercial Registration Number</label>
                        <input type="text" name="registration_number" value="{{ $company->registration_number }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-[#3da9e4]/50 focus:border-[#3da9e4] outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">About Company</label>
                        <textarea name="about" rows="3" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-[#3da9e4]/50 focus:border-[#3da9e4] outline-none text-sm resize-none">{{ $company->about }}</textarea>
                    </div>
                </div>
                <div class="p-5 border-t border-gray-100 bg-gray-50 flex justify-end space-x-3">
                    <button type="button" @click="editModal = false" class="px-4 py-2 text-gray-600 bg-white border border-gray-200 rounded-md font-medium text-sm hover:bg-gray-50 transition-colors">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-[#3da9e4] text-white rounded-md font-medium text-sm hover:bg-[#2b8bc2] transition-colors">Update Company</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Manage Users / Permissions Modal -->
    <div x-show="manageUsersModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
        <div @click.away="manageUsersModal = false" class="bg-white rounded-lg shadow-xl w-full max-w-2xl border border-gray-100 overflow-hidden animate-in zoom-in duration-200">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">Manage Users & Permissions</h2>
                <button @click="manageUsersModal = false" class="text-gray-400 hover:text-gray-600"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            
            <div class="p-6">
                <!-- User List -->
                <div class="border border-gray-200 rounded-lg overflow-hidden mb-6">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-[10px] uppercase tracking-widest text-gray-500">
                            <tr class="divide-x divide-gray-200 border-b border-gray-200">
                                <th class="px-4 py-3 font-semibold">User</th>
                                <th class="px-4 py-3 font-semibold">Role</th>
                                <th class="px-4 py-3 font-semibold">Project Permissions</th>
                                <th class="px-4 py-3 font-semibold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 font-medium text-gray-900">Sarah Ahmed</td>
                                <td class="px-4 py-3 text-gray-600">Finance Manager</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 bg-[#e6f4fd] text-[#3da9e4] rounded text-[10px] font-semibold border border-[#3da9e4]/20">View / Edit</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button class="text-gray-400 hover:text-[#3da9e4] transition-colors"><i data-lucide="settings" class="w-4 h-4 ml-auto"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-100 pt-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Invite New User</h3>
                    <form class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Email Address</label>
                                <input type="email" placeholder="colleague@example.com" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-[#3da9e4]/50 focus:border-[#3da9e4] outline-none text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Role / Job Title</label>
                                <input type="text" placeholder="e.g. Legal Advisor" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-[#3da9e4]/50 focus:border-[#3da9e4] outline-none text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">Project Permissions for this Company</label>
                            <div class="flex space-x-4">
                                <label class="flex items-center space-x-2">
                                    <input type="radio" name="permission" value="view" class="text-[#3da9e4] focus:ring-[#3da9e4]">
                                    <span class="text-sm text-gray-700">View Only</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="radio" name="permission" value="edit" checked class="text-[#3da9e4] focus:ring-[#3da9e4]">
                                    <span class="text-sm text-gray-700">View & Edit</span>
                                </label>
                            </div>
                        </div>
                        <div class="flex justify-end pt-2">
                            <button type="button" class="px-5 py-2.5 bg-gray-900 text-white rounded-md font-medium text-sm hover:bg-black transition-colors flex items-center space-x-2">
                                <i data-lucide="mail" class="w-4 h-4"></i>
                                <span>Send Invitation</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection