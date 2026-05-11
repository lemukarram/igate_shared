<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('landing.site_title_en', 'iGate Shared Services - Standardized B2B Service Marketplace in KSA');
        $this->migrator->add('landing.site_title_ar', 'آي غيت للخدمات المشتركة - سوق خدمات B2B الموحد في المملكة العربية السعودية');
        $this->migrator->add('landing.hero_badge_en', 'The Operating System for Business in KSA');
        $this->migrator->add('landing.hero_badge_ar', 'نظام التشغيل للأعمال في المملكة العربية السعودية');
        $this->migrator->add('landing.hero_title_en', 'Outsource with Absolute Trust.');
        $this->migrator->add('landing.hero_title_ar', 'أسند مهامك بثقة مطلقة.');
        $this->migrator->add('landing.hero_subtitle_en', "Saudi Arabia's first strictly standardized B2B marketplace. Fixed scope, verified agencies, and secure escrow protection.");
        $this->migrator->add('landing.hero_subtitle_ar', 'أول سوق B2B موحد بدقة في المملكة العربية السعودية. نطاق عمل ثابت، وكالات موثوقة، وحماية دفع آمنة.');
        $this->migrator->add('landing.hero_cta_client_en', 'Join as Client');
        $this->migrator->add('landing.hero_cta_client_ar', 'انضم كعميل');
        $this->migrator->add('landing.hero_cta_provider_en', 'Join as a Service Provider');
        $this->migrator->add('landing.hero_cta_provider_ar', 'انضم كمزود خدمة');

        $this->migrator->add('landing.why_title_en', 'Why iGate Shared Services?');
        $this->migrator->add('landing.why_title_ar', 'لماذا آي غيت للخدمات المشتركة؟');
        $this->migrator->add('landing.why_subtitle_en', 'A robust platform designed to eliminate the friction in B2B transactions through standardization and transparency.');
        $this->migrator->add('landing.why_subtitle_ar', 'منصة قوية مصممة لإزالة العقبات في معاملات B2B من خلال التوحيد والشفافية.');
        
        $this->migrator->add('landing.why_features', [
            [
                'icon' => 'lock',
                'title_en' => 'Escrow Security',
                'title_ar' => 'أمان الدفع (الضمان)',
                'desc_en' => 'Funds are securely held in escrow and released only upon verified milestone completion, ensuring trust on both sides.',
                'desc_ar' => 'يتم الاحتفاظ بالأموال بشكل آمن وإصدارها فقط عند اكتمال المراحل التي تم التحقق منها، مما يضمن الثقة لكلا الطرفين.',
            ],
            [
                'icon' => 'layout-grid',
                'title_en' => 'Fixed Scopes',
                'title_ar' => 'نطاقات عمل ثابتة',
                'desc_en' => '12 strictly standardized service types prevent scope creep and guarantee you get exactly what you pay for.',
                'desc_ar' => '12 نوعاً من الخدمات الموحدة بدقة تمنع تجاوز نطاق العمل وتضمن حصولك على ما تدفع مقابله تماماً.',
            ],
            [
                'icon' => 'check-square',
                'title_en' => 'Verified Providers',
                'title_ar' => 'مزودون موثوقون',
                'desc_en' => 'Every agency is thoroughly vetted, KSA-registered, and rated by real clients for consistent quality.',
                'desc_ar' => 'يتم فحص كل وكالة بدقة، وهي مسجلة في المملكة، ومقيمة من قبل عملاء حقيقيين لضمان جودة متسقة.',
            ],
            [
                'icon' => 'activity',
                'title_en' => 'SLA Tracking',
                'title_ar' => 'تتبع اتفاقية مستوى الخدمة (SLA)',
                'desc_en' => 'Automated performance monitoring and centralized task boards keep projects perfectly on schedule.',
                'desc_ar' => 'مراقبة تلقائية للأداء ولوحات مهام مركزية تبقي المشاريع في جدولها الزمني بدقة.',
            ],
        ]);

        $this->migrator->add('landing.services_title_en', 'Core Services Catalog');
        $this->migrator->add('landing.services_title_ar', 'كتالوج الخدمات الأساسية');
        $this->migrator->add('landing.services_subtitle_en', 'Explore our specialized catalog covering essential B2B needs.');
        $this->migrator->add('landing.services_subtitle_ar', 'استكشف كتالوجنا المتخصص الذي يغطي احتياجات B2B الأساسية.');

        $this->migrator->add('landing.pricing_title_en', 'Subscription Plans');
        $this->migrator->add('landing.pricing_title_ar', 'خطط الاشتراك');
        $this->migrator->add('landing.pricing_subtitle_en', 'Select the perfect plan to grow your business with iGate.');
        $this->migrator->add('landing.pricing_subtitle_ar', 'اختر الخطة المثالية لتنمية أعمالك مع iGate.');

        $this->migrator->add('landing.footer_description_en', 'The Operating System for B2B transactions in the Kingdom of Saudi Arabia.');
        $this->migrator->add('landing.footer_description_ar', 'نظام التشغيل لمعاملات B2B في المملكة العربية السعودية.');
    }

    public function down(): void
    {
        $this->migrator->delete('landing.hero_badge_en');
        $this->migrator->delete('landing.hero_badge_ar');
        $this->migrator->delete('landing.hero_title_en');
        $this->migrator->delete('landing.hero_title_ar');
        $this->migrator->delete('landing.hero_subtitle_en');
        $this->migrator->delete('landing.hero_subtitle_ar');
        $this->migrator->delete('landing.hero_cta_client_en');
        $this->migrator->delete('landing.hero_cta_client_ar');
        $this->migrator->delete('landing.hero_cta_provider_en');
        $this->migrator->delete('landing.hero_cta_provider_ar');
        $this->migrator->delete('landing.why_title_en');
        $this->migrator->delete('landing.why_title_ar');
        $this->migrator->delete('landing.why_subtitle_en');
        $this->migrator->delete('landing.why_subtitle_ar');
        $this->migrator->delete('landing.why_features');
        $this->migrator->delete('landing.services_title_en');
        $this->migrator->delete('landing.services_title_ar');
        $this->migrator->delete('landing.services_subtitle_en');
        $this->migrator->delete('landing.services_subtitle_ar');
        $this->migrator->delete('landing.pricing_title_en');
        $this->migrator->delete('landing.pricing_title_ar');
        $this->migrator->delete('landing.pricing_subtitle_en');
        $this->migrator->delete('landing.pricing_subtitle_ar');
        $this->migrator->delete('landing.footer_description_en');
        $this->migrator->delete('landing.footer_description_ar');
        $this->migrator->delete('landing.twitter_url');
        $this->migrator->delete('landing.linkedin_url');
    }
};
