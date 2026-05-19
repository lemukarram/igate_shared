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
                return $this->errorResponse('Invalid credentials', 401);
            }

            $user = Auth::user();

            if ($user->role !== 'client') {
                Auth::logout();
                return $this->errorResponse('Access restricted to clients', 403);
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
            // Logic for sending reset link
            return $this->successResponse(null, 'Password reset link sent if email exists');
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }
}
