<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('common.auth_title') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400&display=swap');
        body { font-family: 'Poppins', sans-serif; font-weight: 300; }
        h1, h2, h3, h4, .font-normal { font-weight: 400 !important; }
        .theme-bg { background-color: #3da9e4; }
        .theme-text { color: #3da9e4; }
        .theme-border { border-color: #3da9e4; }
        .theme-hover-bg:hover { background-color: #2b8bc2; }
        .theme-focus-ring:focus { --tw-ring-color: rgba(61, 169, 228, 0.5); border-color: #3da9e4; }
        [dir="rtl"] .ml-2 { margin-left: 0; margin-right: 0.5rem; }
    </style>
</head>
<body class="bg-gray-50 flex flex-col items-center justify-center min-h-screen p-6">
    <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-lg border border-gray-100">
        <div class="text-center mb-8">
            <a href="/">
                <img src="/images/logo/logo.png" alt="iGate Shared Services" class="h-10 mx-auto mb-6 object-contain">
            </a>
            <h1 class="text-2xl font-normal text-gray-900">{{ __('common.reset_password') }}</h1>
            <p class="text-gray-500 mt-2 text-sm">{{ __('common.reset_password_subtitle') }}</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 text-red-500 p-3 rounded-lg text-sm mb-6 border border-red-100">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            
            <div>
                <label class="block text-sm font-normal text-gray-700 mb-1.5">{{ __('common.email_address') }}</label>
                <input type="email" name="email" required placeholder="name@company.com" value="{{ request()->email ?? old('email') }}"
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-4 theme-focus-ring transition-all outline-none text-sm">
            </div>

            <div>
                <label class="block text-sm font-normal text-gray-700 mb-1.5">{{ __('common.new_password') }}</label>
                <input type="password" name="password" required placeholder="••••••••" minlength="8"
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-4 theme-focus-ring transition-all outline-none text-sm">
            </div>

            <div>
                <label class="block text-sm font-normal text-gray-700 mb-1.5">{{ __('common.confirm_password') }}</label>
                <input type="password" name="password_confirmation" required placeholder="••••••••" minlength="8"
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-4 theme-focus-ring transition-all outline-none text-sm">
            </div>

            <button type="submit" class="w-full py-3.5 theme-bg text-white rounded-lg font-normal text-sm theme-hover-bg transition-all shadow-md active:scale-95">
                {{ __('common.update_password') }}
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-100 text-center text-sm text-gray-500 font-normal">
            <a href="{{ route('login') }}" class="theme-text hover:underline font-normal">{{ __('common.back_to_login') }}</a>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
