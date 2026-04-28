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
                
                $teamMembers = [];
                if (Auth::user()->role === 'provider') {
                    $team = \App\Models\Team::where('owner_id', Auth::id())->first();
                    if ($team) {
                        $teamMembers = $team->members()->with('user')->get();
                    }
                }
                
                $view->with('ongoingProjects', $ongoingProjects)
                     ->with('teamMembers', collect($teamMembers));
            } else {
                $view->with('ongoingProjects', collect())
                     ->with('teamMembers', collect());
            }
        });
    }
}
