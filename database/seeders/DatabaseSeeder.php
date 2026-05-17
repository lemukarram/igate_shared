<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ProviderProfile;
use App\Models\Service;
use App\Models\ProviderService;
use App\Models\Project;
use App\Models\Payment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Initialize Shield Permissions
        Artisan::call('shield:generate', ['--all' => true, '--panel' => 'admin']);

        // Seed Service Categories
        $categories = [
            ['name' => 'HR & Recruitment', 'slug' => 'hr-recruitment'],
            ['name' => 'Financial Services', 'slug' => 'financial-services'],
            ['name' => 'Legal & Compliance', 'slug' => 'legal-compliance'],
            ['name' => 'Marketing & Sales', 'slug' => 'marketing-sales'],
        ];

        foreach ($categories as $cat) {
            \App\Models\ServiceCategory::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        // 1. Seed Services (12 Fixed Catalog)
        $this->call(ServiceSeeder::class);
        $this->call(PlanSeeder::class);

        // Link services to categories (Mock)
        $cats = \App\Models\ServiceCategory::all();
        \App\Models\Service::all()->each(function($s) use ($cats) {
            $s->update(['service_category_id' => $cats->random()->id]);
        });

        $basicProviderPlan = \App\Models\Plan::where('name', 'Basic')->where('type', 'provider')->first();
        $basicClientPlan = \App\Models\Plan::where('name', 'Basic')->where('type', 'client')->first();
        $enterpriseProviderPlan = \App\Models\Plan::where('name', 'Enterprise')->where('type', 'provider')->first();

        // 2. Seed Super Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@igate.com'],
            [
                'name' => 'iGate Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Ensure Super Admin role exists and is assigned (Filament Shield)
        $superAdminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => config('filament-shield.super_admin.name', 'super_admin')]);
        $admin->assignRole($superAdminRole);

        // Seed Payment Settings from .env if available
        $paymentSettings = app(\App\Settings\PaymentSettings::class);
        $paymentSettings->environment = env('TAP_PAYMENT_MODE', 'sandbox');
        $paymentSettings->sandbox_secret_key = env('TAP_SANDBOX_SECRET_KEY', $paymentSettings->sandbox_secret_key);
        $paymentSettings->live_secret_key = env('TAP_LIVE_SECRET_KEY', $paymentSettings->live_secret_key);
        $paymentSettings->merchant_id = env('TAP_MERCHANT_ID', $paymentSettings->merchant_id);
        $paymentSettings->save();

        // 3. Seed Providers
        $hrUser = User::updateOrCreate(
            ['email' => 'hr@provider.com'],
            [
                'name' => 'Expert HR Solutions',
                'password' => Hash::make('password'),
                'role' => 'provider',
                'plan_id' => $enterpriseProviderPlan->id,
            ]
        );

        ProviderProfile::updateOrCreate(
            ['user_id' => $hrUser->id],
            [
                'company_name' => 'Expert HR Solutions',
                'bio' => 'Professional HR services providing top-tier B2B solutions in Saudi Arabia.',
                'onboarding_completed' => true,
                'status' => 'verified'
            ]
        );

        $otherProviders = [
            ['name' => 'Tax Compliance KSA', 'email' => 'tax@provider.com'],
            ['name' => 'Legal Pro Agency', 'email' => 'legal@provider.com'],
        ];

        foreach ($otherProviders as $pData) {
            $user = User::updateOrCreate(
                ['email' => $pData['email']],
                [
                    'name' => $pData['name'],
                    'password' => Hash::make('password'),
                    'role' => 'provider',
                    'plan_id' => $basicProviderPlan->id,
                ]
            );

            ProviderProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'company_name' => $pData['name'],
                    'bio' => 'Professional ' . $pData['name'] . ' providing top-tier B2B services.',
                    'onboarding_completed' => true,
                    'status' => 'verified'
                ]
            );
        }

        // 4. Seed Clients
        $clientUser = User::updateOrCreate(
            ['email' => 'client@igate.com'],
            [
                'name' => 'Retail Corp',
                'password' => Hash::make('password'),
                'role' => 'client',
                'plan_id' => $basicClientPlan->id,
            ]
        );

        $clientCompany = \App\Models\Company::firstOrCreate(
            ['client_id' => $clientUser->id, 'name' => 'Retail Corp KSA'],
            ['industry' => 'Retail', 'about' => 'Leading retail corporation in Saudi Arabia.']
        );

        // 5. Seed Active Projects for Demo
        $hrService = Service::where('name', 'HR Management')->first();
        $accountingService = Service::where('name', 'Accounting & Bookkeeping')->first();
        $legalService = Service::where('name', 'Legal Contract Review')->first();

        // Ensure HR Provider offers these services
        $servicesToAssign = [$hrService, $accountingService, $legalService];
        foreach ($servicesToAssign as $s) {
            if ($s) {
                $monthlyPrice = rand(2000, 5000);
                ProviderService::updateOrCreate(
                    ['provider_id' => $hrUser->id, 'service_id' => $s->id],
                    [
                        'monthly_price' => $monthlyPrice,
                        'annual_price' => $monthlyPrice * 10,
                        'delivery_time_days' => 14
                    ]
                );
            }
        }

        // Create Active Engagements
        $demoProjects = [
            ['service' => $hrService, 'amount' => 3500],
            ['service' => $accountingService, 'amount' => 5000],
            ['service' => $legalService, 'amount' => 2800],
        ];

        foreach ($demoProjects as $dp) {
            if (!$dp['service']) continue;

            $ps = ProviderService::where('provider_id', $hrUser->id)
                ->where('service_id', $dp['service']->id)
                ->first();

            $project = Project::create([
                'client_id' => $clientUser->id,
                'company_id' => $clientCompany->id,
                'provider_id' => $hrUser->id,
                'service_id' => $dp['service']->id,
                'provider_service_id' => $ps ? $ps->id : null,
                'status' => 'active',
                'total_amount' => $dp['amount'],
                'start_date' => now(),
            ]);
            Payment::create([
                'project_id' => $project->id,
                'user_id' => $clientUser->id,
                'amount' => $dp['amount'],
                'payment_method' => 'card',
                'transaction_id' => 'TXN-' . strtoupper(Str::random(10)),
                'status' => 'held_in_escrow',
            ]);
        }
    }
}
