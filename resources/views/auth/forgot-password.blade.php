<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - iGate Shared Services</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');
        body { font-family: 'Poppins', sans-serif; }
        .theme-bg { background-color: #3da9e4; }
        .theme-text { color: #3da9e4; }
        .theme-focus-ring:focus { --tw-ring-color: rgba(61, 169, 228, 0.5); border-color: #3da9e4; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-6">
    <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-lg border border-gray-100">
        <div class="text-center mb-8">
            <a href="/">
                <img src="/images/logo/logo.png" alt="iGate Shared Services" class="h-10 mx-auto mb-6 object-contain">
            </a>
            <h1 class="text-2xl font-semibold text-gray-900">Reset Password</h1>
            <p class="text-gray-500 mt-2 text-sm">Enter your email to receive a reset link</p>
        </div>

        @if(session('status'))
            <div class="bg-[#e6f4fd] text-[#3da9e4] p-3 rounded-lg text-sm mb-6 border border-[#3da9e4]/30 font-medium text-center">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                <input type="email" name="email" required placeholder="name@company.com"
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-4 theme-focus-ring transition-all outline-none text-sm">
                @error('email') <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="w-full py-3.5 theme-bg text-white rounded-lg font-medium text-sm hover:bg-[#2b8bc2] transition-all shadow-md active:scale-95">
                Send Reset Link
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-100 text-center text-sm font-medium">
            <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-800 transition-colors">← Back to Login</a>
        </div>
    </div>
</body>
</html>