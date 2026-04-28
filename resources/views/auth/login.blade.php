<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication - iGate Shared Services</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');
        body { font-family: 'Poppins', sans-serif; }
        .theme-bg { background-color: #3da9e4; }
        .theme-text { color: #3da9e4; }
        .theme-border { border-color: #3da9e4; }
        .theme-hover-bg:hover { background-color: #2b8bc2; }
        .theme-focus-ring:focus { --tw-ring-color: rgba(61, 169, 228, 0.5); border-color: #3da9e4; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-6">
    <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-lg border border-gray-100">
        <div class="text-center mb-8">
            <a href="/">
                <img src="/images/logo/logo.png" alt="iGate Shared Services" class="h-10 mx-auto mb-6 object-contain">
            </a>
            <h1 class="text-2xl font-semibold text-gray-900" id="form-title">Welcome back</h1>
            <p class="text-gray-500 mt-2 text-sm" id="form-subtitle">Sign in to manage your B2B services</p>
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
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                <input type="email" name="email" required placeholder="name@company.com" value="{{ old('email') }}"
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-4 theme-focus-ring transition-all outline-none text-sm">
            </div>

            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <a href="{{ route('password.request') }}" class="text-sm theme-text font-medium hover:underline">Forgot password?</a>
                </div>
                <input type="password" name="password" required placeholder="••••••••"
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-4 theme-focus-ring transition-all outline-none text-sm">
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-[#3da9e4] border-gray-300 rounded focus:ring-[#3da9e4]">
                <label for="remember" class="ml-2 text-sm text-gray-600 font-medium">Remember me</label>
            </div>

            <button type="submit" class="w-full py-3.5 theme-bg text-white rounded-lg font-medium text-sm theme-hover-bg transition-all shadow-md active:scale-95">
                Sign In
            </button>
        </form>

        <!-- Register Form (Hidden by default) -->
        <form id="register-form" action="{{ route('register.post') }}" method="POST" class="space-y-4 hidden">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name / Company Name</label>
                <input type="text" name="name" required placeholder="Your Name" value="{{ old('name') }}"
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-4 theme-focus-ring transition-all outline-none text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                <input type="email" name="email" required placeholder="name@company.com" value="{{ old('email') }}"
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-4 theme-focus-ring transition-all outline-none text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone Number</label>
                <input type="text" name="phone" required placeholder="+966 5X XXX XXXX" value="{{ old('phone') }}"
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-4 theme-focus-ring transition-all outline-none text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                <input type="password" name="password" required placeholder="••••••••" minlength="8"
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-4 theme-focus-ring transition-all outline-none text-sm">
            </div>

            <div class="pt-2">
                <div class="flex items-center mb-3 p-3 bg-[#f0f8fd] border border-[#3da9e4]/30 rounded-lg">
                    <input type="checkbox" name="join_as_provider" id="join_as_provider" value="1" class="w-4 h-4 text-[#3da9e4] border-gray-300 rounded focus:ring-[#3da9e4]">
                    <label for="join_as_provider" class="ml-2 text-sm text-gray-800 font-semibold cursor-pointer">Join as a Service Provider</label>
                </div>
                <div class="flex items-start">
                    <input type="checkbox" name="agree_terms" id="agree_terms" required class="w-4 h-4 mt-0.5 text-[#3da9e4] border-gray-300 rounded focus:ring-[#3da9e4]">
                    <label for="agree_terms" class="ml-2 text-sm text-gray-600 font-medium">I agree to the <a href="/terms" class="theme-text hover:underline" target="_blank">Terms & Conditions</a></label>
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 mt-2 theme-bg text-white rounded-lg font-medium text-sm theme-hover-bg transition-all shadow-md active:scale-95">
                Create Account
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-100 text-center text-sm text-gray-500 font-medium">
            <p id="toggle-text">
                Don't have an account? <button type="button" onclick="toggleForms()" class="theme-text hover:underline ml-1 font-semibold">Sign Up</button>
            </p>
        </div>
    </div>

    <script>
        let isLogin = true;
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
                title.textContent = 'Welcome back';
                subtitle.textContent = 'Sign in to manage your B2B services';
                toggleText.innerHTML = `Don't have an account? <button type="button" onclick="toggleForms()" class="theme-text hover:underline ml-1 font-semibold">Sign Up</button>`;
            } else {
                loginForm.classList.add('hidden');
                registerForm.classList.remove('hidden');
                title.textContent = 'Create an Account';
                subtitle.textContent = 'Join the standardized B2B marketplace';
                toggleText.innerHTML = `Already have an account? <button type="button" onclick="toggleForms()" class="theme-text hover:underline ml-1 font-semibold">Sign In</button>`;
            }
        }

        // If there are validation errors on registration, show register form
        @if($errors->has('name') || $errors->has('phone') || $errors->has('agree_terms'))
            toggleForms();
        @endif
    </script>
</body>
</html>