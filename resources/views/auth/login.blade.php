<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('common.auth_title') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
        [dir="rtl"] .ml-1 { margin-left: 0; margin-right: 0.25rem; }
        [dir="rtl"] .pl-5 { padding-left: 0; padding-right: 1.25rem; }
    </style>
</head>
<body class="bg-gray-50 flex flex-col items-center justify-center min-h-screen p-6">
    <div class="fixed top-6 right-6 z-50">
        <button onclick="toggleLang()" class="flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-100 rounded-lg hover:bg-gray-50 transition-all shadow-sm">
            <i data-lucide="languages" class="w-4 h-4 text-gray-500"></i>
            <span class="text-xs font-normal uppercase text-gray-700">{{ app()->getLocale() === 'en' ? 'العربية' : 'English' }}</span>
        </button>
    </div>
    <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-lg border border-gray-100">
        <div class="text-center mb-8">
            <a href="/">
                <img src="/images/logo/logo.png" alt="iGate Shared Services" class="h-10 mx-auto mb-6 object-contain">
            </a>
            <h1 class="text-2xl font-normal text-gray-900" id="form-title">{{ __('common.welcome_back') }}</h1>
            <p class="text-gray-500 mt-2 text-sm" id="form-subtitle">{{ __('common.signin_subtitle') }}</p>
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

        <!-- Login Form -->
        <form id="login-form" action="{{ route('login.post') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-normal text-gray-700 mb-1.5">{{ __('common.email_address') }}</label>
                <input type="email" name="email" required placeholder="name@company.com" value="{{ old('email') }}"
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-4 theme-focus-ring transition-all outline-none text-sm">
            </div>

            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <label class="block text-sm font-normal text-gray-700">{{ __('common.password') }}</label>
                    <a href="{{ route('password.request') }}" class="text-sm theme-text font-normal hover:underline">{{ __('common.forgot_password') }}</a>
                </div>
                <input type="password" name="password" required placeholder="••••••••"
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-4 theme-focus-ring transition-all outline-none text-sm">
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-[#3da9e4] border-gray-300 rounded focus:ring-[#3da9e4]">
                <label for="remember" class="ml-2 text-sm text-gray-600 font-normal">{{ __('common.remember_me') }}</label>
            </div>

            <button type="submit" class="w-full py-3.5 theme-bg text-white rounded-lg font-normal text-sm theme-hover-bg transition-all shadow-md active:scale-95">
                {{ __('common.signin') }}
            </button>
        </form>

        <!-- Register Form (Hidden by default) -->
        <form id="register-form" action="{{ route('register.post') }}" method="POST" class="space-y-4 hidden">
            @csrf
            <div>
                <label class="block text-sm font-normal text-gray-700 mb-1.5">{{ __('common.fullname_company') }}</label>
                <input type="text" name="name" required placeholder="{{ __('common.name') }}" value="{{ old('name') }}"
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-4 theme-focus-ring transition-all outline-none text-sm">
            </div>
            <div>
                <label class="block text-sm font-normal text-gray-700 mb-1.5">{{ __('common.email_address') }}</label>
                <input type="email" name="email" required placeholder="name@company.com" value="{{ old('email') }}"
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-4 theme-focus-ring transition-all outline-none text-sm">
            </div>
            <div>
                <label class="block text-sm font-normal text-gray-700 mb-1.5">{{ __('common.phone_number') }}</label>
                <input type="text" name="phone" required placeholder="+966 5X XXX XXXX" value="{{ old('phone') }}"
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-4 theme-focus-ring transition-all outline-none text-sm">
            </div>
            <div>
                <label class="block text-sm font-normal text-gray-700 mb-1.5">{{ __('common.password') }}</label>
                <input type="password" name="password" required placeholder="••••••••" minlength="8"
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-4 theme-focus-ring transition-all outline-none text-sm">
            </div>

            <div class="pt-2">
                <div class="flex items-center mb-3 p-3 bg-[#f0f8fd] border border-[#3da9e4]/30 rounded-lg">
                    <input type="checkbox" name="join_as_provider" id="join_as_provider" value="1" class="w-4 h-4 text-[#3da9e4] border-gray-300 rounded focus:ring-[#3da9e4]">
                    <label for="join_as_provider" class="ml-2 text-sm text-gray-800 font-normal cursor-pointer">{{ __('common.join_as_provider') }}</label>
                </div>
                <div class="flex items-start">
                    <input type="checkbox" name="agree_terms" id="agree_terms" required class="w-4 h-4 mt-0.5 text-[#3da9e4] border-gray-300 rounded focus:ring-[#3da9e4]">
                    <label for="agree_terms" class="ml-2 text-sm text-gray-600 font-normal">{{ __('common.agree_terms') }} <a href="/terms" class="theme-text hover:underline" target="_blank">{{ __('common.terms_conditions') }}</a></label>
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 mt-2 theme-bg text-white rounded-lg font-normal text-sm theme-hover-bg transition-all shadow-md active:scale-95">
                {{ __('common.create_account') }}
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-100 text-center text-sm text-gray-500 font-normal">
            <p id="toggle-text">
                {{ __('common.dont_have_account') }} <button type="button" onclick="toggleForms()" class="theme-text hover:underline ml-1 font-normal">{{ __('common.signup') }}</button>
            </p>
        </div>
    </div>

    <script>
        let isLogin = true;
        const dict = {
            welcome_back: "{{ __('common.welcome_back') }}",
            signin_subtitle: "{{ __('common.signin_subtitle') }}",
            signup: "{{ __('common.signup') }}",
            dont_have_account: "{{ __('common.dont_have_account') }}",
            create_account_title: "{{ __('common.create_account_title') }}",
            join_marketplace_subtitle: "{{ __('common.join_marketplace_subtitle') }}",
            already_have_account: "{{ __('common.already_have_account') }}",
            signin: "{{ __('common.signin') }}"
        };

        function toggleForms() {
            isLogin = !isLogin;
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            const title = document.getElementById('form-title');
            const subtitle = document.getElementById('form-subtitle');
            const toggleText = document.getElementById('toggle-text');

            if (isLogin) {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
                title.textContent = dict.welcome_back;
                subtitle.textContent = dict.signin_subtitle;
                toggleText.innerHTML = `${dict.dont_have_account} <button type="button" onclick="toggleForms()" class="theme-text hover:underline ml-1 font-normal">${dict.signup}</button>`;
            } else {
                loginForm.classList.add('hidden');
                registerForm.classList.remove('hidden');
                title.textContent = dict.create_account_title;
                subtitle.textContent = dict.join_marketplace_subtitle;
                toggleText.innerHTML = `${dict.already_have_account} <button type="button" onclick="toggleForms()" class="theme-text hover:underline ml-1 font-normal">${dict.signin}</button>`;
            }
        }

        // If there are validation errors on registration, show register form
        @if($errors->has('name') || $errors->has('phone') || $errors->has('agree_terms'))
            toggleForms();
        @endif

        lucide.createIcons();
        function toggleLang() {
            const currentLang = "{{ app()->getLocale() }}";
            const newLang = currentLang === 'en' ? 'ar' : 'en';
            localStorage.setItem('igate_lang', newLang);
            document.cookie = "igate_lang=" + newLang + ";path=/;max-age=" + (365 * 24 * 60 * 60);
            location.reload();
        }
    </script>
</body>
</html>