<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - iGate Shared Services</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center h-screen p-6">
    <div class="max-w-md w-full bg-white p-10 rounded-[2.5rem] shadow-xl shadow-blue-100/50 border border-gray-100">
        <div class="text-center mb-10">
            <img src="/images/logo/logo.png" alt="iGate Shared Services" class="h-12 mx-auto mb-8 object-contain">
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Welcome back</h1>
            <p class="text-gray-500 mt-2">Sign in to manage your B2B services</p>
        </div>

        <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Email Address</label>
                <input type="email" name="email" required placeholder="name@company.com"
                       class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all outline-none">
                @error('email') <p class="text-red-500 text-xs mt-2 font-semibold">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Password</label>
                <input type="password" name="password" required placeholder="••••••••"
                       class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all outline-none">
            </div>

            <button type="submit" class="w-full py-5 bg-blue-600 text-white rounded-2xl font-bold text-lg hover:bg-blue-700 transition-all shadow-xl shadow-blue-200 active:scale-[0.98]">
                Sign In
            </button>
        </form>

        <div class="mt-10 pt-8 border-t border-gray-100 text-center text-sm text-gray-400">
            <p>Don't have an account? <a href="#" class="text-blue-600 font-bold hover:underline">Contact Sales</a></p>
        </div>
    </div>
</body>
</html>
