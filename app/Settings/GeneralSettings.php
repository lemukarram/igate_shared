<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name;
    public string $contact_email;
    public string $contact_phone;
    public string $default_currency;
    public float $platform_fee_percentage;
    public string $default_language;
    public string $logo;
    public string $collapsed_logo;
    
    // Protection Block
    public string $protection_block_title;
    public string $protection_block_description;
    public string $protection_block_bg_color;
    public array $protection_block_points;

    public static function group(): string
    {
        return 'general';
    }
}
