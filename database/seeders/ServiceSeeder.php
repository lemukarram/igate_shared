<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'HR Management',
                'description' => 'Standardized HR services including employee onboarding and policy management.',
                'category' => 'Operations',
                'icon' => 'users'
            ],
            [
                'name' => 'ZATCA Compliance',
                'description' => 'Ensure your business meets KSA E-Invoicing (Fatoora) requirements.',
                'category' => 'Compliance',
                'icon' => 'shield-check'
            ],
            [
                'name' => 'SEO & Digital Marketing',
                'description' => 'Standardized digital visibility and search engine optimization package.',
                'category' => 'Marketing',
                'icon' => 'trending-up'
            ],
            [
                'name' => 'Legal Contract Review',
                'description' => 'Professional legal review of business contracts and agreements.',
                'category' => 'Legal',
                'icon' => 'scale'
            ],
            [
                'name' => 'Accounting & Bookkeeping',
                'description' => 'Monthly financial recording and reporting for SMEs.',
                'category' => 'Finance',
                'icon' => 'calculator'
            ],
            [
                'name' => 'IT Support & Security',
                'description' => 'Managed IT infrastructure and cybersecurity audit.',
                'category' => 'Technology',
                'icon' => 'monitor'
            ],
            [
                'name' => 'Translation Services',
                'description' => 'Certified Arabic-English document translation.',
                'category' => 'Administrative',
                'icon' => 'languages'
            ],
            [
                'name' => 'Corporate Tax Filing',
                'description' => 'End-of-year tax returns and financial compliance.',
                'category' => 'Finance',
                'icon' => 'file-text'
            ],
            [
                'name' => 'Social Media Management',
                'description' => 'Standardized posting and engagement for business profiles.',
                'category' => 'Marketing',
                'icon' => 'share-2'
            ],
            [
                'name' => 'Payroll Processing',
                'description' => 'Automated monthly salary disbursement and WPS compliance.',
                'category' => 'Operations',
                'icon' => 'credit-card'
            ],
            [
                'name' => 'Trademark Registration',
                'description' => 'Intellectual property protection within KSA.',
                'category' => 'Legal',
                'icon' => 'award'
            ],
            [
                'name' => 'Business Plan Development',
                'description' => 'Structured business planning and market analysis.',
                'category' => 'Strategy',
                'icon' => 'map'
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(['name' => $service['name']], $service);
        }
    }
}
