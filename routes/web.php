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
        if (Auth::user()->role === 'admin') {
            return view('welcome'); // Admin view
        }
        return redirect()->route('explore.index');
    }
    return view('landing');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth', 'App\Http\Middleware\EnsureProviderIsOnboarded'])->group(function () {
    Route::get('/provider/dashboard', [App\Http\Controllers\ProviderDashboardController::class, 'index'])->name('provider.dashboard');

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
    Route::get('/provider/pre-sale-chats', [App\Http\Controllers\PreSaleChatController::class, 'index'])->name('provider.pre_sale_chats.index');
    Route::get('/explore/{serviceId}/provider/{providerId}/chat', [App\Http\Controllers\PreSaleChatController::class, 'show'])->name('explore.chat');
    Route::post('/explore/{serviceId}/provider/{providerId}/chat', [App\Http\Controllers\PreSaleChatController::class, 'sendMessage'])->name('explore.chat.send');

    // Client Portfolio and Companies
    Route::get('/portfolio', [MarketplaceController::class, 'portfolio'])->name('client.portfolio');
    Route::get('/my-providers', [MarketplaceController::class, 'myProviders'])->name('client.my_providers');
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
    Route::post('/projects/{id}/messages', [ProjectController::class, 'sendMessage'])->name('projects.messages.send');
    Route::post('/projects/{id}/complete', [ProjectController::class, 'complete'])->name('projects.complete');
    Route::post('/projects/{id}/approve', [ProjectController::class, 'approve'])->name('projects.approve');
    Route::post('/projects/{id}/reject', [ProjectController::class, 'reject'])->name('projects.reject');
    Route::get('/projects/{id}/history', [ProjectController::class, 'history'])->name('projects.history');
    Route::post('/projects/{id}/cancel-request', [ProjectController::class, 'requestCancellation'])->name('projects.cancel-request');
    Route::post('/projects/{id}/confirm-cancellation', [ProjectController::class, 'confirmCancellation'])->name('projects.confirm-cancellation');
    Route::post('/projects/{id}/dispute', [ProjectController::class, 'dispute'])->name('projects.dispute');
    Route::post('/projects/{id}/terminate', [ProjectController::class, 'terminate'])->name('projects.terminate');

    // Settings
    Route::post('/settings/profile', [App\Http\Controllers\SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::post('/settings/status', [App\Http\Controllers\SettingsController::class, 'updateStatus'])->name('settings.status');
    Route::post('/settings/company', [App\Http\Controllers\SettingsController::class, 'updateCompany'])->name('settings.company');
    Route::post('/settings/general', [App\Http\Controllers\SettingsController::class, 'updateGeneral'])->name('settings.general');
    Route::post('/settings/notifications', [App\Http\Controllers\SettingsController::class, 'updateNotifications'])->name('settings.notifications');
    Route::post('/settings/security', [App\Http\Controllers\SettingsController::class, 'updateSecurity'])->name('settings.security');
    Route::post('/settings/plan', [App\Http\Controllers\SettingsController::class, 'updatePlan'])->name('settings.plan');
    Route::patch('/settings/team-members/{id}', [App\Http\Controllers\SettingsController::class, 'updateTeamMember'])->name('settings.team_members.update');
    Route::post('/settings/team-members', [App\Http\Controllers\SettingsController::class, 'addTeamMember'])->name('settings.team_members.store');
    Route::delete('/settings/team-members/{id}', [App\Http\Controllers\SettingsController::class, 'removeTeamMember'])->name('settings.team_members.destroy');
    Route::get('/settings/plan/upgrade', function() {
        $plans = \App\Models\Plan::where('type', Auth::user()->role)->get();
        return view('settings.upgrade_plan', compact('plans'));
    })->name('settings.plan.upgrade');

    // Internal Messages
    Route::get('/internal-messages', [App\Http\Controllers\InternalMessageController::class, 'index'])->name('internal-messages.index');
    Route::post('/internal-messages', [App\Http\Controllers\InternalMessageController::class, 'store'])->name('internal-messages.store');

    // Tasks
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{id}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
    Route::post('/tasks/{id}/verify', [TaskController::class, 'verify'])->name('tasks.verify');

    // Milestones
    Route::post('/milestones', [MilestoneController::class, 'store'])->name('milestones.store');
    Route::patch('/milestones/{id}/status', [MilestoneController::class, 'updateStatus'])->name('milestones.updateStatus');

    // Documents
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::delete('/documents/{id}', [App\Http\Controllers\TeamTaskController::class, 'deleteDocument'])->name('documents.destroy');

    // Reviews
    Route::post('/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
});
