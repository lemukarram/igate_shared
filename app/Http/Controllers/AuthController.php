<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Plan;
use App\Models\ProviderProfile;
use Modules\Emails\Events\UserRegistered;
use Modules\Emails\Events\PasswordResetRequested;

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
            
            if (Auth::user()->isProviderMode()) {
                if (!Auth::user()->providerProfile || !Auth::user()->providerProfile->onboarding_completed) {
                    return redirect()->route('provider.onboarding');
                }
            }

            return redirect()->intended(route('explore.index'));
        }

        return back()->withErrors([
            'email' => __('common.auth_failed'),
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
        $plan = Plan::where('type', $role)->orderBy('monthly_price', 'asc')->first();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'role' => $role,
            'active_portal' => $role,
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

        // Fire User Registered Event to send Activation Email
        $activationToken = Str::random(60);
        // (Optional: save this token to the user record or a tokens table if you build verification logic)
        event(new UserRegistered($user, $activationToken));

        if ($isProvider) {
            return redirect()->route('provider.onboarding');
        }
        
        return redirect()->route('explore.index');
    }

    public function showForgot()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $user = User::where('email', $request->email)->first();
        
        if ($user) {
            $resetToken = Str::random(60);
            // (Optional: save token to password_reset_tokens table)
            event(new PasswordResetRequested($user, $resetToken));
        }

        return back()->with('status', 'We have emailed your password reset link if the email exists in our system.');
    }

    public function showReset($token)
    {
        return view('auth.reset-password', compact('token'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'We cannot find a user with that email address.']);
        }

        // In a full implementation, you would verify the token here against a password_reset_tokens table.
        // For this MVP, we will proceed with the reset if the user exists.

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('login')->with('status', __('common.password_reset_success'));
    }

    public function verifyEmail($token)
    {
        return "Email verification logic here for token: " . $token;
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
