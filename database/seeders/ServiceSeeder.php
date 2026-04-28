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
                'icon' => 'users',
                'subtasks' => ['Employee Onboarding', 'Policy Development', 'Performance Reviews', 'Compliance Audit']
            ],
            [
                'name' => 'ZATCA Compliance',
                'description' => 'Ensure your business meets KSA E-Invoicing (Fatoora) requirements.',
                'category' => 'Compliance',
                'icon' => 'shield-check',
                'subtasks' => ['Phase 1 Implementation', 'Phase 2 Integration', 'XML Validation', 'Device Registration']
            ],
            [
                'name' => 'SEO & Digital Marketing',
                'description' => 'Standardized digital visibility and search engine optimization package.',
                'category' => 'Marketing',
                'icon' => 'trending-up',
                'subtasks' => ['Keyword Research', 'On-page SEO', 'Backlink Building', 'Monthly Analytics Report']
            ],
            [
                'name' => 'Legal Contract Review',
                'description' => 'Professional legal review of business contracts and agreements.',
                'category' => 'Legal',
                'icon' => 'scale',
                'subtasks' => ['Risk Assessment', 'Clause Analysis', 'Drafting Amendments', 'Final Approval']
            ],
            [
                'name' => 'Accounting & Bookkeeping',
                'description' => 'Monthly financial recording and reporting for SMEs.',
                'category' => 'Finance',
                'icon' => 'calculator',
                'subtasks' => ['Monthly Ledgers', 'Bank Reconciliation', 'Financial Statements', 'VAT Filing Prep']
            ],
            [
                'name' => 'IT Support & Security',
                'description' => 'Managed IT infrastructure and cybersecurity audit.',
                'category' => 'Technology',
                'icon' => 'monitor',
                'subtasks' => ['Network Setup', 'Security Firewall Audit', 'Data Backup Config', '24/7 Monitoring']
            ],
            [
                'name' => 'Translation Services',
                'description' => 'Certified Arabic-English document translation.',
                'category' => 'Administrative',
                'icon' => 'languages',
                'subtasks' => ['Document Assessment', 'Certified Translation', 'Proofreading', 'Legalization Support']
            ],
            [
                'name' => 'Corporate Tax Filing',
                'description' => 'End-of-year tax returns and financial compliance.',
                'category' => 'Finance',
                'icon' => 'file-text',
                'subtasks' => ['Income Statement Review', 'Tax Liability Calculation', 'ZATCA Submission', 'Payment Tracking']
            ],
            [
                'name' => 'Social Media Management',
                'description' => 'Standardized posting and engagement for business profiles.',
                'category' => 'Marketing',
                'icon' => 'share-2',
                'subtasks' => ['Content Calendar', 'Graphic Design', 'Community Engagement', 'Ad Campaign Mgmt']
            ],
            [
                'name' => 'Payroll Processing',
                'description' => 'Automated monthly salary disbursement and WPS compliance.',
                'category' => 'Operations',
                'icon' => 'credit-card',
                'subtasks' => ['WPS File Generation', 'Bank Portal Upload', 'Payslip Issuance', 'GOSI Reconciliation']
            ],
            [
                'name' => 'Trademark Registration',
                'description' => 'Intellectual property protection within KSA.',
                'category' => 'Legal',
                'icon' => 'award',
                'subtasks' => ['Search & Availability', 'Filing Application', 'Opposition Monitoring', 'Certificate Issuance']
            ],
            [
                'name' => 'Business Plan Development',
                'description' => 'Structured business planning and market analysis.',
                'category' => 'Strategy',
                'icon' => 'map',
                'subtasks' => ['Market Analysis', 'Financial Forecasting', 'Strategy Definition', 'Pitch Deck Design']
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(['name' => $service['name']], $service);
        }
    }
}
