<?php

namespace Modules\ClientAPI\GraphQL\Mutations;

use App\Models\User;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthMutation
{
    /**
     * Handle client login and return token.
     */
    public function login($_, array $args)
    {
        $input = $args['input'];

        if (!Auth::attempt($input)) {
            throw ValidationException::withMessages([
                'email' => [__('common.auth_failed')],
            ]);
        }

        $user = Auth::user();

        // Strict restriction to Client role for this API
        if ($user->role !== 'client') {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => [__('common.access_denied_clients')],
            ]);
        }

        return [
            'user' => $user,
            'token' => $user->createToken('client_mobile_app')->plainTextToken,
        ];
    }

    /**
     * Handle client registration.
     */
    public function register($_, array $args)
    {
        $input = $args['input'];

        // Find the basic client plan
        $plan = Plan::where('type', 'client')->orderBy('monthly_price', 'asc')->first();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'] ?? null,
            'password' => Hash::make($input['password']),
            'role' => 'client',
            'plan_id' => $plan ? $plan->id : null,
        ]);

        return [
            'user' => $user,
            'token' => $user->createToken('client_mobile_app')->plainTextToken,
        ];
    }

    /**
     * Placeholder for forgot password logic.
     */
    public function forgotPassword($_, array $args)
    {
        // Integration with standard Laravel password broker can be added here
        return [
            'status' => 'SUCCESS',
            'message' => 'If an account with that email exists, we have sent a password reset link.',
        ];
    }

    /**
     * Placeholder for reset password logic.
     */
    public function resetPassword($_, array $args)
    {
        return [
            'status' => 'SUCCESS',
            'message' => 'Your password has been successfully reset.',
        ];
    }
}
