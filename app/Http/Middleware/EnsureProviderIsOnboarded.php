<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureProviderIsOnboarded
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->role === 'provider') {
            $profile = $user->providerProfile;

            if (!$profile || !$profile->onboarding_completed) {
                if (!$request->routeIs('provider.onboarding*')) {
                    return redirect()->route('provider.onboarding');
                }
            }
        }

        return $next($request);
    }
}
