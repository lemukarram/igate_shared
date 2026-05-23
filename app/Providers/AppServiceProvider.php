<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\View;
use App\Models\Project;
use App\Models\PreSaleMessage;
use Illuminate\Support\Facades\Auth;
use App\Settings\GeneralSettings;
use App\Settings\PaymentSettings;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Filament\Notifications\Notification::configureUsing(function (\Filament\Notifications\Notification $notification): void {
            $notification->duration(10000);
        });

        View::composer('*', function ($view) {
            $settings = app(GeneralSettings::class);
            $paymentSettings = app(PaymentSettings::class);
            $view->with('settings', $settings)
                 ->with('paymentSettings', $paymentSettings);

            if (Auth::check()) {
                $ongoingProjects = Project::where(function($query) {
                        $query->where('client_id', Auth::id())
                              ->orWhere('provider_id', Auth::id());
                    })
                    ->where('status', 'active')
                    ->with(['service', 'provider.providerProfile', 'client'])
                    ->latest()
                    ->get();
                
                $teamMembers = [];
                if (Auth::user()->isProviderMode()) {
                    $team = \App\Models\Team::where('owner_id', Auth::id())->first();
                    if ($team) {
                        $teamMembers = $team->members()->with('user')->get();
                    }
                }
                
                $view->with('ongoingProjects', $ongoingProjects)
                     ->with('teamMembers', collect($teamMembers));

                // Add Pre-Sale Chats
                if (Auth::user()->isClientMode()) {
                    $preSaleChats = PreSaleMessage::where('client_id', Auth::id())
                        ->with(['service', 'provider.providerProfile'])
                        ->latest()
                        ->get()
                        ->unique(function ($item) {
                            return $item->provider_id . '-' . $item->service_id;
                        });
                    $view->with('preSaleChats', $preSaleChats);
                } elseif (Auth::user()->isProviderMode()) {
                    $preSaleChats = PreSaleMessage::where('provider_id', Auth::id())
                        ->with(['service', 'client'])
                        ->latest()
                        ->get()
                        ->unique(function ($item) {
                            return $item->client_id . '-' . $item->service_id;
                        });
                    $view->with('preSaleChats', $preSaleChats);
                } else {
                    $view->with('preSaleChats', collect());
                }

                // Share entities for permissions scoping
                if (Auth::user()->isClientMode()) {
                    $view->with('permission_companies', Auth::user()->companies()->where('is_active', true)->get());
                    $view->with('permission_projects', Auth::user()->projects()->where('status', 'active')->get());
                    $view->with('permission_clients', collect()); // Clients don't have other clients
                } elseif (Auth::user()->isProviderMode()) {
                    $view->with('permission_companies', collect()); // Providers don't have companies in this context
                    $view->with('permission_projects', Auth::user()->providerProjects()->where('status', 'active')->get());
                    $activeProjects = Auth::user()->providerProjects()->where('status', 'active');
                    $uniqueClientIds = $activeProjects->pluck('client_id')->unique();
                    $view->with('permission_clients', \App\Models\User::whereIn('id', $uniqueClientIds)->get());
                }
            } else {
                $view->with('ongoingProjects', collect())
                     ->with('teamMembers', collect())
                     ->with('permission_companies', collect())
                     ->with('permission_projects', collect())
                     ->with('permission_clients', collect());
            }
        });
    }
}
