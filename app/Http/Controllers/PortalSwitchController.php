<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalSwitchController extends Controller
{
    public function switch(Request $request, $mode)
    {
        if (!in_array($mode, ['client', 'provider'])) {
            return back()->with('error', 'Invalid portal mode.');
        }

        $user = Auth::user();

        $user->update(['active_portal' => $mode]);
        session(['active_portal' => $mode]);

        // If switching to provider, ensure they have a profile or redirect to onboarding
        if ($mode === 'provider' && (!$user->providerProfile || !$user->providerProfile->onboarding_completed)) {
            return redirect()->route('provider.onboarding')->with('info', 'Please complete your provider profile to access the provider portal.');
        }

        $message = $mode === 'provider' ? 'Switched to Provider Portal' : 'Switched to Client Portal';
        
        // Redirect to respective dashboard
        $route = $mode === 'provider' ? 'provider.dashboard' : 'client.dashboard';
        
        return redirect()->route($route)->with('success', $message);
    }
}
