@extends('layouts.app')

@section('content')
<div class="max-w-7xl w-full space-y-8 animate-in fade-in duration-700">
    <div class="flex items-center gap-4">
        <a href="{{ route('client.portfolio') }}" class="w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-900 transition-colors">
            <i data-lucide="arrow-left" class="w-5 h-5 flip-rtl"></i>
        </a>
        <div>
            <h1 class="text-3xl font-normal text-gray-900">{{ $company->name }}</h1>
            <p class="text-sm text-gray-500 font-normal" x-text="t('common.manage_company_subtitle')"></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            <!-- Projects Section -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-normal text-gray-900" x-text="t('common.projects')"></h2>
                    <a href="{{ route('explore.index') }}" class="px-4 py-2 bg-primary text-white rounded-xl text-sm font-normal hover:bg-primary-dark transition-all flex items-center gap-2 shadow-md">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span x-text="t('common.request_service')"></span>
                    </a>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    @forelse($company->projects as $project)
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-primary-light rounded-xl flex items-center justify-center text-primary">
                                <i data-lucide="{{ $project->service->icon }}" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h4 class="font-normal text-gray-900">{{ $project->service->name }}</h4>
                                <p class="text-xs text-gray-400 font-normal">{{ $project->provider->providerProfile->company_name ?? 'iGate Partner' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-[10px] font-normal uppercase px-2 py-0.5 rounded-md {{ $project->status === 'active' ? 'text-green-500 bg-green-50' : 'text-gray-500 bg-gray-50' }}" x-text="t('project.status_{{ $project->status }}')"></span>
                            <a href="{{ route('projects.show', $project->id) }}" class="text-primary hover:text-primary-dark transition-colors">
                                <i data-lucide="chevron-right" class="w-5 h-5 flip-rtl"></i>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="py-12 text-center bg-gray-50 rounded-3xl border border-dashed border-gray-200">
                        <p class="text-gray-500 font-normal" x-text="t('common.no_projects_yet')"></p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Pre-sale Chats Section -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-normal text-gray-900" x-text="t('common.pre_sale_chats')"></h2>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    @forelse($preSaleChats as $chat)
                    <div class="bg-blue-50/30 border border-blue-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-blue-500 border border-blue-100">
                                <i data-lucide="message-circle" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h4 class="font-normal text-gray-900">{{ $chat->service->name }}</h4>
                                <p class="text-xs text-gray-500 font-normal">{{ $chat->provider->providerProfile->company_name }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-[10px] font-normal uppercase px-2 py-0.5 rounded-md text-blue-500 bg-blue-100/50" x-text="t('common.consultation')"></span>
                            <a href="{{ route('explore.chat', [$chat->service_id, $chat->provider_id]) }}" class="text-blue-500 hover:text-blue-700 transition-colors">
                                <i data-lucide="chevron-right" class="w-5 h-5 flip-rtl"></i>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="py-12 text-center bg-gray-50 rounded-3xl border border-dashed border-gray-200">
                        <p class="text-gray-500 font-normal" x-text="t('common.no_pre_sale_chats_yet')"></p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <h2 class="text-xl font-normal text-gray-900" x-text="t('common.company_profile')"></h2>
            <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-sm">
                <div class="flex justify-center mb-8">
                    @if($company->logo)
                        <img src="{{ asset('storage/' . $company->logo) }}" class="w-24 h-24 rounded-2xl object-cover border border-gray-100 shadow-sm">
                    @else
                        <div class="w-24 h-24 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-300 border-2 border-dashed border-gray-200">
                            <i data-lucide="image" class="w-10 h-10"></i>
                        </div>
                    @endif
                </div>
                <div class="space-y-6">
                    <div>
                        <p class="text-[10px] font-normal text-gray-400 uppercase tracking-widest mb-1" x-text="t('common.industry')"></p>
                        <p class="text-sm font-normal text-gray-900">{{ $company->industry ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-normal text-gray-400 uppercase tracking-widest mb-1" x-text="t('common.registration_number')"></p>
                        <p class="text-sm font-normal text-gray-900">{{ $company->registration_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-normal text-gray-400 uppercase tracking-widest mb-1" x-text="t('common.about')"></p>
                        <p class="text-sm font-normal text-gray-600 leading-relaxed">{{ $company->about ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-normal text-gray-400 uppercase tracking-widest mb-1" x-text="t('common.documents')"></p>
                        @php
                            $documents = Auth::user()->documents()->where('name', 'like', '%' . $company->name . '%')->get();
                        @endphp
                        <div class="space-y-2 mt-2">
                            @forelse($documents as $doc)
                                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="flex items-center p-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors group">
                                    <i data-lucide="file-text" class="w-4 h-4 text-gray-400 mr-2"></i>
                                    <span class="text-xs font-normal text-gray-700 truncate flex-1">{{ $doc->name }}</span>
                                    <i data-lucide="download" class="w-3 h-3 text-gray-300 group-hover:text-primary transition-colors"></i>
                                </a>
                            @empty
                                <p class="text-xs text-gray-400 italic" x-text="t('common.no_documents_uploaded')"></p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="mt-8 pt-8 border-t border-gray-50">
                    <button onclick="document.getElementById('edit-company-modal').classList.remove('hidden')" class="w-full py-3 bg-gray-900 text-white rounded-xl text-sm font-normal hover:bg-black transition-all flex items-center justify-center gap-2 shadow-lg shadow-gray-200">
                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                        <span x-text="t('common.edit_company')"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Company Modal -->
<div id="edit-company-modal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-[2.5rem] p-10 max-w-xl w-full mx-4 shadow-2xl animate-in zoom-in duration-300 max-h-[90vh] overflow-y-auto custom-scrollbar">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-normal text-gray-900" x-text="t('common.edit_company')"></h3>
            <button onclick="document.getElementById('edit-company-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-900"><i data-lucide="x" class="w-6 h-6"></i></button>
        </div>
        <form action="{{ route('companies.update', $company->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="flex justify-center mb-6">
                <div class="relative group">
                    <img id="logo-preview" src="{{ $company->logo ? asset('storage/' . $company->logo) : '' }}" class="w-24 h-24 rounded-2xl object-cover border-2 border-primary/10 {{ $company->logo ? '' : 'hidden' }}">
                    <div id="logo-placeholder" class="w-24 h-24 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-300 border-2 border-dashed border-gray-200 {{ $company->logo ? 'hidden' : '' }}">
                        <i data-lucide="image" class="w-10 h-10"></i>
                    </div>
                    <label class="absolute -bottom-2 -right-2 w-8 h-8 bg-white border border-gray-100 rounded-full flex items-center justify-center shadow-lg cursor-pointer hover:bg-gray-50 transition-all text-primary">
                        <i data-lucide="camera" class="w-4 h-4"></i>
                        <input type="file" name="logo" class="hidden" onchange="previewImage(this, 'logo-preview', 'logo-placeholder')">
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label class="text-[10px] font-normal uppercase tracking-widest text-gray-400" x-text="t('common.company_name')"></label>
                    <input type="text" name="name" value="{{ $company->name }}" required class="w-full px-5 py-3 border border-gray-100 bg-gray-50 rounded-2xl outline-none focus:ring-4 focus:ring-primary/10 transition-all font-normal text-sm">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-normal uppercase tracking-widest text-gray-400" x-text="t('common.industry')"></label>
                    <select name="industry" class="w-full px-5 py-3 border border-gray-100 bg-gray-50 rounded-2xl outline-none focus:ring-4 focus:ring-primary/10 transition-all font-normal text-sm">
                        @foreach($industries as $industry)
                            <option value="{{ $industry->name }}" {{ $company->industry === $industry->name ? 'selected' : '' }} x-text="lang === 'ar' ? '{{ $industry->name_ar }}' : '{{ $industry->name }}'"></option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-normal uppercase tracking-widest text-gray-400" x-text="t('common.registration_number')"></label>
                <input type="text" name="registration_number" value="{{ $company->registration_number }}" class="w-full px-5 py-3 border border-gray-100 bg-gray-50 rounded-2xl outline-none focus:ring-4 focus:ring-primary/10 transition-all font-normal text-sm">
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-normal uppercase tracking-widest text-gray-400" x-text="t('common.about')"></label>
                <textarea name="about" rows="4" class="w-full px-5 py-4 border border-gray-100 bg-gray-50 rounded-2xl outline-none focus:ring-4 focus:ring-primary/10 transition-all font-normal text-sm resize-none">{{ $company->about }}</textarea>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-normal uppercase tracking-widest text-gray-400" x-text="t('common.upload_documents')"></label>
                <input type="file" name="documents[]" multiple class="w-full px-5 py-3 border border-gray-100 bg-gray-50 rounded-2xl outline-none focus:ring-4 focus:ring-primary/10 transition-all font-normal text-sm">
                <p class="text-[10px] text-gray-400 mt-1" x-text="t('common.upload_documents_hint')"></p>
            </div>

            <button type="submit" class="w-full py-4 bg-primary text-white rounded-2xl font-normal text-sm hover:bg-primary-dark transition-all shadow-xl shadow-primary/20">
                <span x-text="t('common.save_changes')"></span>
            </button>
        </form>
    </div>
</div>

<style>
    [dir="rtl"] .flip-rtl { transform: scaleX(-1); }
</style>
@endsection
