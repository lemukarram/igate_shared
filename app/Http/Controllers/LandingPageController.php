<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Plan;
use App\Settings\LandingPageSettings;
use Illuminate\Support\Facades\Auth;

class LandingPageController extends Controller
{
    public function index(LandingPageSettings $settings, \App\Settings\GeneralSettings $generalSettings)
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return view('welcome');
            }
            if (Auth::user()->isProviderMode()) {
                return redirect()->route('provider.dashboard');
            }
            return redirect()->route('client.dashboard');
        }

        $services = Service::where('is_active', true)->take(3)->get();
        $plans = Plan::all();

        return view('landing', [
            'landingSettings' => $settings,
            'services' => $services,
            'plans' => $plans,
            'generalSettings' => $generalSettings
        ]);
    }
}
