@extends('layouts.app')

@section('content')
<div class="max-w-2xl w-full bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-gray-900">Step 1: Business Details</h2>
            <span class="text-sm font-medium text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Step 1 of 3</span>
        </div>
        <p class="text-gray-500">Tell us about your agency or freelance business.</p>
    </div>

    <form action="{{ route('provider.onboarding.step1.post') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Company Name</label>
            <input type="text" name="company_name" value="{{ old('company_name', $profile->company_name) }}" required
                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Brief Bio / Description</label>
            <textarea name="bio" rows="4"
                      class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all">{{ old('bio', $profile->bio) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Company Logo</label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-200 border-dashed rounded-xl hover:border-blue-400 transition-colors cursor-pointer bg-gray-50">
                <div class="space-y-1 text-center">
                    <i data-lucide="image" class="mx-auto h-12 w-12 text-gray-400"></i>
                    <div class="flex text-sm text-gray-600">
                        <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                            <span>Upload a file</span>
                            <input id="file-upload" name="logo" type="file" class="sr-only" accept="image/*">
                        </label>
                        <p class="pl-1">or drag and drop</p>
                    </div>
                    <p class="text-xs text-gray-500">PNG, JPG up to 2MB</p>
                </div>
            </div>
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full py-4 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-100 flex items-center justify-center space-x-2">
                <span>Next: Legal & Compliance</span>
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
            </button>
        </div>
    </form>
</div>
@endsection
