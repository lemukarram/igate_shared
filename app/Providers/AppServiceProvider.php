<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\View;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

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
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $ongoingProjects = Project::where(function($query) {
                        $query->where('client_id', Auth::id())
                              ->orWhere('provider_id', Auth::id());
                    })
                    ->where('status', 'active')
                    ->with(['service', 'provider.providerProfile', 'client'])
                    ->latest()
                    ->get();
                
                $view->with('ongoingProjects', $ongoingProjects);
            } else {
                $view->with('ongoingProjects', collect());
            }
        });
    }
}
