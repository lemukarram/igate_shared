<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providerPlans = [
            [
                'name' => 'Basic',
                'description' => 'Perfect for individual freelancers starting out.',
                'type' => 'provider',
                'price' => 0.00,
                'max_services' => 1,
                'max_users' => 1,
                'max_projects' => 1,
                'max_companies' => 0,
                'features' => ['1 Standardized Service', '1 Team Member', '1 Active Project'],
            ],
            [
                'name' => 'Professional',
                'description' => 'Ideal for small agencies and growing teams.',
                'type' => 'provider',
                'price' => 99.00,
                'max_services' => 3,
                'max_users' => 3,
                'max_projects' => 3,
                'max_companies' => 0,
                'features' => ['3 Standardized Services', '3 Team Members', '3 Active Projects', 'Priority Support'],
            ],
            [
                'name' => 'Enterprise',
                'description' => 'For large agencies requiring full platform power.',
                'type' => 'provider',
                'price' => 399.00,
                'max_services' => 999, // Unlimited
                'max_users' => 999, // Unlimited
                'max_projects' => 999, // Unlimited
                'max_companies' => 0,
                'features' => ['Unlimited Services', 'Unlimited Team Members', 'Unlimited Projects', 'Dedicated Account Manager', 'Advanced Analytics'],
            ],
        ];

        $clientPlans = [
            [
                'name' => 'Basic',
                'description' => 'Basic access for small businesses.',
                'type' => 'client',
                'price' => 0.00,
                'max_services' => 1,
                'max_users' => 1,
                'max_projects' => 1,
                'max_companies' => 1,
                'features' => ['1 Managed Company', '1 User Account', '1 Active Service Request'],
            ],
            [
                'name' => 'Professional',
                'description' => 'Professional features for growing businesses.',
                'type' => 'client',
                'price' => 99.00,
                'max_services' => 3,
                'max_users' => 3,
                'max_projects' => 3,
                'max_companies' => 3,
                'features' => ['3 Managed Companies', '3 User Accounts', '3 Active Service Requests', 'Priority Support'],
            ],
            [
                'name' => 'Enterprise',
                'description' => 'Full enterprise-grade management for large corps.',
                'type' => 'client',
                'price' => 399.00,
                'max_services' => 999, // Unlimited
                'max_users' => 999, // Unlimited
                'max_projects' => 999, // Unlimited
                'max_companies' => 999, // Unlimited
                'features' => ['Unlimited Companies', 'Unlimited Users', 'Unlimited Projects', 'Dedicated Support', 'Custom Reporting'],
            ],
        ];

        foreach (array_merge($providerPlans, $clientPlans) as $planData) {
            Plan::updateOrCreate(
                ['name' => $planData['name'], 'type' => $planData['type']],
                $planData
            );
        }
    }
}
