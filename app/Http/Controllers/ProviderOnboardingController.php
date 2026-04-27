<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProviderProfile;

class ProviderOnboardingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $profile = $user->providerProfile ?? new ProviderProfile();
        
        if ($profile->onboarding_completed) {
            return redirect()->route('provider.dashboard');
        }

        // Default to step 1
        return view('provider.onboarding.step1', compact('profile'));
    }

    public function postStep1(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();
        $profile = ProviderProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'company_name' => $validated['company_name'],
                'bio' => $validated['bio'] ?? '',
            ]
        );

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $profile->update(['logo' => $path]);
        }

        return redirect()->route('provider.onboarding.step2');
    }

    public function step2()
    {
        $profile = Auth::user()->providerProfile;
        return view('provider.onboarding.step2', compact('profile'));
    }

    public function postStep2(Request $request)
    {
        $validated = $request->validate([
            'commercial_registration' => 'required|string|max:255',
            'tax_number' => 'required|string|max:255',
        ]);

        Auth::user()->providerProfile->update($validated);

        return redirect()->route('provider.onboarding.step3');
    }

    public function step3()
    {
        $profile = Auth::user()->providerProfile;
        return view('provider.onboarding.step3', compact('profile'));
    }

    public function postStep3(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'iban' => 'required|string|max:255',
        ]);

        Auth::user()->providerProfile->update(array_merge($validated, [
            'onboarding_completed' => true,
            'status' => 'pending'
        ]));

        return redirect()->route('provider.dashboard')->with('success', 'Onboarding completed! Your profile is now under review.');
    }
}
