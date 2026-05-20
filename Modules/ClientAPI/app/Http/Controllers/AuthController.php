<?php

namespace Modules\ClientAPI\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use App\Traits\HandlesApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use HandlesApiResponses;

    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'nullable|string',
            ]);

            $plan = Plan::where('type', 'client')->orderBy('monthly_price', 'asc')->first();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => 'client',
                'plan_id' => $plan ? $plan->id : null,
            ]);

            $token = $user->createToken('client_mobile_app')->plainTextToken;

            return $this->successResponse([
                'user' => $user,
                'token' => $token,
            ], 'Registration successful', 201);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (!Auth::attempt($request->only('email', 'password'))) {
                return $this->errorResponse(__('common.auth_failed'), 401);
            }

            $user = Auth::user();

            if ($user->role !== 'client') {
                Auth::logout();
                return $this->errorResponse(__('common.access_denied_clients'), 403);
            }

            $token = $user->createToken('client_mobile_app')->plainTextToken;

            return $this->successResponse([
                'user' => $user,
                'token' => $token,
            ], 'Login successful');
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function me()
    {
        return $this->successResponse(Auth::user()->load(['companies', 'projects']));
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();
            
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'phone' => 'sometimes|string',
            ]);

            $user->update($validated);

            return $this->successResponse($user, 'Profile updated successfully');
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function forgotPassword(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email']);
            
            $user = User::where('email', $request->email)->first();
            
            if ($user) {
                $token = \Illuminate\Support\Str::random(60);
                event(new \Modules\Emails\Events\PasswordResetRequested($user, $token));
            }

            return $this->successResponse(null, 'If an account with that email exists, we have sent a password reset link.');
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return $this->errorResponse('User not found', 404);
            }

            $user->password = Hash::make($request->password);
            $user->save();

            return $this->successResponse(null, 'Password reset successful');
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return $this->successResponse(null, 'Logout successful');
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }
}
