<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Plan;
use App\Models\ProviderProfile;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            if (Auth::user()->role === 'provider' && (!Auth::user()->providerProfile || !Auth::user()->providerProfile->onboarding_completed)) {
                return redirect()->route('provider.onboarding');
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8',
            'agree_terms' => 'required|accepted',
        ]);

        $isProvider = $request->has('join_as_provider');
        $role = $isProvider ? 'provider' : 'client';
        $plan = Plan::where('type', $role)->where('name', 'Basic')->first();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'role' => $role,
            'plan_id' => $plan ? $plan->id : null,
        ]);

        if ($isProvider) {
            ProviderProfile::create([
                'user_id' => $user->id,
                'company_name' => $data['name'],
                'status' => 'pending',
                'onboarding_completed' => false,
            ]);
        }

        Auth::login($user);

        if ($isProvider) {
            return redirect()->route('provider.onboarding');
        }
        
        return redirect()->intended('/');
    }

    public function showForgot()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        return back()->with('status', 'We have emailed your password reset link if the email exists in our system.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
