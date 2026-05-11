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
                'name_ar' => 'إدارة الموارد البشرية',
                'description' => 'Standardized HR services including employee onboarding and policy management.',
                'description_ar' => 'خدمات الموارد البشرية الموحدة بما في ذلك تهيئة الموظفين وإدارة السياسات.',
                'category' => 'Operations',
                'icon' => 'users',
                'subtasks' => ['Employee Onboarding', 'Policy Development', 'Performance Reviews', 'Compliance Audit']
            ],
            [
                'name' => 'ZATCA Compliance',
                'name_ar' => 'الامتثال لهيئة الزكاة والضريبة والجمارك',
                'description' => 'Ensure your business meets KSA E-Invoicing (Fatoora) requirements.',
                'description_ar' => 'تأكد من استيفاء عملك لمتطلبات الفوترة الإلكترونية (فاتورة) في المملكة العربية السعودية.',
                'category' => 'Compliance',
                'icon' => 'shield-check',
                'subtasks' => ['Phase 1 Implementation', 'Phase 2 Integration', 'XML Validation', 'Device Registration']
            ],
            [
                'name' => 'SEO & Digital Marketing',
                'name_ar' => 'تحسين محركات البحث والتسويق الرقمي',
                'description' => 'Standardized digital visibility and search engine optimization package.',
                'description_ar' => 'باقة موحدة للظهور الرقمي وتحسين محركات البحث.',
                'category' => 'Marketing',
                'icon' => 'trending-up',
                'subtasks' => ['Keyword Research', 'On-page SEO', 'Backlink Building', 'Monthly Analytics Report']
            ],
            [
                'name' => 'Legal Contract Review',
                'name_ar' => 'مراجعة العقود القانونية',
                'description' => 'Professional legal review of business contracts and agreements.',
                'description_ar' => 'مراجعة قانونية مهنية للعقود والاتفاقيات التجارية.',
                'category' => 'Legal',
                'icon' => 'scale',
                'subtasks' => ['Risk Assessment', 'Clause Analysis', 'Drafting Amendments', 'Final Approval']
            ],
            [
                'name' => 'Accounting & Bookkeeping',
                'name_ar' => 'المحاسبة ومسك الدفاتر',
                'description' => 'Monthly financial recording and reporting for SMEs.',
                'description_ar' => 'التسجيل والتقارير المالية الشهرية للمؤسسات الصغيرة والمتوسطة.',
                'category' => 'Finance',
                'icon' => 'calculator',
                'subtasks' => ['Monthly Ledgers', 'Bank Reconciliation', 'Financial Statements', 'VAT Filing Prep']
            ],
            [
                'name' => 'IT Support & Security',
                'name_ar' => 'الدعم الفني وأمن المعلومات',
                'description' => 'Managed IT infrastructure and cybersecurity audit.',
                'description_ar' => 'إدارة البنية التحتية لتكنولوجيا المعلومات وفحص الأمن السيبراني.',
                'category' => 'Technology',
                'icon' => 'monitor',
                'subtasks' => ['Network Setup', 'Security Firewall Audit', 'Data Backup Config', '24/7 Monitoring']
            ],
            [
                'name' => 'Translation Services',
                'name_ar' => 'خدمات الترجمة',
                'description' => 'Certified Arabic-English document translation.',
                'description_ar' => 'ترجمة معتمدة للوثائق بين اللغتين العربية والإنجليزية.',
                'category' => 'Administrative',
                'icon' => 'languages',
                'subtasks' => ['Document Assessment', 'Certified Translation', 'Proofreading', 'Legalization Support']
            ],
            [
                'name' => 'Corporate Tax Filing',
                'name_ar' => 'تقديم الإقرارات الضريبية للشركات',
                'description' => 'End-of-year tax returns and financial compliance.',
                'description_ar' => 'الإقرارات الضريبية لنهاية العام والامتثال المالي.',
                'category' => 'Finance',
                'icon' => 'file-text',
                'subtasks' => ['Income Statement Review', 'Tax Liability Calculation', 'ZATCA Submission', 'Payment Tracking']
            ],
            [
                'name' => 'Social Media Management',
                'name_ar' => 'إدارة وسائل التواصل الاجتماعي',
                'description' => 'Standardized posting and engagement for business profiles.',
                'description_ar' => 'نشر وتفاعل موحد للملفات الشخصية للأعمال.',
                'category' => 'Marketing',
                'icon' => 'share-2',
                'subtasks' => ['Content Calendar', 'Graphic Design', 'Community Engagement', 'Ad Campaign Mgmt']
            ],
            [
                'name' => 'Payroll Processing',
                'name_ar' => 'معالجة الرواتب',
                'description' => 'Automated monthly salary disbursement and WPS compliance.',
                'description_ar' => 'صرف الرواتب الشهري الآلي والامتثال لنظام حماية الأجور (WPS).',
                'category' => 'Operations',
                'icon' => 'credit-card',
                'subtasks' => ['WPS File Generation', 'Bank Portal Upload', 'Payslip Issuance', 'GOSI Reconciliation']
            ],
            [
                'name' => 'Trademark Registration',
                'name_ar' => 'تسجيل العلامات التجارية',
                'description' => 'Intellectual property protection within KSA.',
                'description_ar' => 'حماية الملكية الفكرية داخل المملكة العربية السعودية.',
                'category' => 'Legal',
                'icon' => 'award',
                'subtasks' => ['Search & Availability', 'Filing Application', 'Opposition Monitoring', 'Certificate Issuance']
            ],
            [
                'name' => 'Business Plan Development',
                'name_ar' => 'تطوير خطة العمل',
                'description' => 'Structured business planning and market analysis.',
                'description_ar' => 'تخطيط أعمال منظم وتحليل للسوق.',
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
