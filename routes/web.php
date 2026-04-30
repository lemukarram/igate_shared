<?php

use App\Http\Controllers\ProviderOnboardingController;
use App\Http\Controllers\ProviderServiceController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::get('/forgot-password', [AuthController::class, 'showForgot'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
});

// Landing Page for guests, Dashboard for auth users
Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->role === 'provider') {
            return redirect()->route('provider.dashboard');
        }
        if (Auth::user()->role === 'admin') {
            return view('welcome'); // Admin view
        }
        return view('client.dashboard'); // Client dashboard
    }
    return view('landing');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth', 'App\Http\Middleware\EnsureProviderIsOnboarded'])->group(function () {
    Route::get('/provider/dashboard', function () {
        return view('provider.dashboard');
    })->name('provider.dashboard');

    // Onboarding
    Route::get('/provider/onboarding', [ProviderOnboardingController::class, 'index'])->name('provider.onboarding');
    Route::post('/provider/onboarding/step1', [ProviderOnboardingController::class, 'postStep1'])->name('provider.onboarding.step1.post');
    Route::get('/provider/onboarding/step2', [ProviderOnboardingController::class, 'step2'])->name('provider.onboarding.step2');
    Route::post('/provider/onboarding/step2', [ProviderOnboardingController::class, 'postStep2'])->name('provider.onboarding.step2.post');
    Route::get('/provider/onboarding/step3', [ProviderOnboardingController::class, 'step3'])->name('provider.onboarding.step3');
    Route::post('/provider/onboarding/step3', [ProviderOnboardingController::class, 'postStep3'])->name('provider.onboarding.step3.post');

    // Provider Portfolio
    Route::get('/provider/portfolio', [ProviderServiceController::class, 'index'])->name('provider.services.index');
    Route::post('/provider/portfolio', [ProviderServiceController::class, 'store'])->name('provider.services.store');
    Route::patch('/provider/portfolio/{id}', [ProviderServiceController::class, 'update'])->name('provider.services.update');
    Route::delete('/provider/portfolio/{id}', [ProviderServiceController::class, 'destroy'])->name('provider.services.destroy');

    // Client Explore
    Route::get('/explore', [MarketplaceController::class, 'index'])->name('explore.index');
    Route::get('/explore/{id}', [MarketplaceController::class, 'show'])->name('explore.show');

    // Provider Specifics
    Route::get('/provider/clients', [ProviderServiceController::class, 'clients'])->name('provider.clients');
    Route::get('/provider/clients/{id}', [ProviderServiceController::class, 'clientShow'])->name('provider.clients.show');
    Route::post('/provider/release-requests', [ProviderServiceController::class, 'storeReleaseRequest'])->name('provider.release-requests.store');
    
    // Team Tasks
    Route::get('/provider/team-tasks', [App\Http\Controllers\TeamTaskController::class, 'index'])->name('provider.team_tasks');
    Route::post('/provider/team-tasks', [App\Http\Controllers\TeamTaskController::class, 'store'])->name('provider.team_tasks.store');
    Route::get('/provider/team-tasks/{id}', [App\Http\Controllers\TeamTaskController::class, 'show'])->name('provider.team_tasks.show');
    Route::patch('/provider/team-tasks/{id}', [App\Http\Controllers\TeamTaskController::class, 'update'])->name('provider.team_tasks.update');
    Route::patch('/provider/team-tasks/{id}/status', [App\Http\Controllers\TeamTaskController::class, 'updateStatus'])->name('provider.team_tasks.status');

    // Pre-sale Chat
    Route::get('/explore/{serviceId}/provider/{providerId}/chat', [MarketplaceController::class, 'preChat'])->name('explore.chat');

    // Client Portfolio and Companies
    Route::get('/portfolio', [MarketplaceController::class, 'portfolio'])->name('client.portfolio');
    Route::post('/companies', [MarketplaceController::class, 'storeCompany'])->name('companies.store');
    Route::get('/companies/{id}', [MarketplaceController::class, 'showCompany'])->name('companies.show');
    Route::put('/companies/{id}', [MarketplaceController::class, 'updateCompany'])->name('companies.update');
    Route::delete('/companies/{id}', [MarketplaceController::class, 'destroyCompany'])->name('companies.destroy');

    // Checkout
    Route::get('/checkout/{providerServiceId}', [CheckoutController::class, 'review'])->name('checkout.review');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');

    // Project Workspace
    Route::get('/projects/{id}', [ProjectController::class, 'show'])->name('projects.show');
    Route::patch('/projects/{id}/status', [ProjectController::class, 'updateStatus'])->name('projects.update-status');
    Route::post('/projects/{id}/messages', [ProjectController::class, 'sendMessage'])->name('projects.messages.store');

    // Settings
    Route::post('/settings/profile', [App\Http\Controllers\SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::post('/settings/company', [App\Http\Controllers\SettingsController::class, 'updateCompany'])->name('settings.company');
    Route::post('/settings/security', [App\Http\Controllers\SettingsController::class, 'updateSecurity'])->name('settings.security');
    Route::post('/settings/plan', [App\Http\Controllers\SettingsController::class, 'updatePlan'])->name('settings.plan');
    Route::patch('/settings/team-members/{id}', [App\Http\Controllers\SettingsController::class, 'updateTeamMember'])->name('settings.team_members.update');

    // Tasks
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{id}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');

    // Milestones
    Route::post('/milestones', [MilestoneController::class, 'store'])->name('milestones.store');
    Route::patch('/milestones/{id}/status', [MilestoneController::class, 'updateStatus'])->name('milestones.updateStatus');

    // Documents
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
});
