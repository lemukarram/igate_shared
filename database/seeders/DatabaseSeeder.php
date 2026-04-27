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

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Services (12 Fixed Catalog)
        $this->call(ServiceSeeder::class);

        // 2. Seed Super Admin
        User::updateOrCreate(
            ['email' => 'admin@igate.com'],
            [
                'name' => 'iGate Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // 3. Seed Providers
        $hrUser = User::updateOrCreate(
            ['email' => 'hr@provider.com'],
            [
                'name' => 'Expert HR Solutions',
                'password' => Hash::make('password'),
                'role' => 'provider',
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
            ]
        );

        // 5. Seed Active Projects for Demo
        $hrService = Service::where('name', 'HR Management')->first();
        $accountingService = Service::where('name', 'Accounting & Bookkeeping')->first();
        $legalService = Service::where('name', 'Legal Contract Review')->first();

        // Ensure HR Provider offers these services
        $servicesToAssign = [$hrService, $accountingService, $legalService];
        foreach ($servicesToAssign as $s) {
            if ($s) {
                ProviderService::updateOrCreate(
                    ['provider_id' => $hrUser->id, 'service_id' => $s->id],
                    ['price' => rand(2000, 5000), 'delivery_time_days' => 14]
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
            
            $project = Project::create([
                'client_id' => $clientUser->id,
                'provider_id' => $hrUser->id,
                'service_id' => $dp['service']->id,
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
