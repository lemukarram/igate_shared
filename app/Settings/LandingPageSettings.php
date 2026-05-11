<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class LandingPageSettings extends Settings
{
    public string $site_title_en;
    public string $site_title_ar;
    public string $hero_badge_en;
    public string $hero_badge_ar;
    public string $hero_title_en;
    public string $hero_title_ar;
    public string $hero_subtitle_en;
    public string $hero_subtitle_ar;
    public string $hero_cta_client_en;
    public string $hero_cta_client_ar;
    public string $hero_cta_provider_en;
    public string $hero_cta_provider_ar;

    public string $why_title_en;
    public string $why_title_ar;
    public string $why_subtitle_en;
    public string $why_subtitle_ar;
    public array $why_features;

    public string $services_title_en;
    public string $services_title_ar;
    public string $services_subtitle_en;
    public string $services_subtitle_ar;

    public string $pricing_title_en;
    public string $pricing_title_ar;
    public string $pricing_subtitle_en;
    public string $pricing_subtitle_ar;

    public string $footer_description_en;
    public string $footer_description_ar;

    public string $twitter_url;
    public string $linkedin_url;

    public static function group(): string
    {
        return 'landing';
    }
}
