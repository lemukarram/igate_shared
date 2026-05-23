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
    public string $client_theme_color;
    public string $provider_theme_color;
    public string $logo;
    public string $collapsed_logo;

    // Logo Customization
    public string $logo_circle_padding;
    public string $logo_circle_padding_collapsed;
    public string $logo_icon_size;
    public string $logo_icon_size_collapsed;
    public string $logo_text_gap;
    public string $logo_text_content;
    public string $logo_text_color;
    public string $logo_text_size;
    public string $logo_text_weight;

    // Contextual URLs
    public string $services_url = 'https://services.igate.com';
    public string $finance_url = 'https://finance.igate.com';
    public string $enterprise_url = 'https://enterprise.igate.com';
    
    // Protection Block
    public string $protection_block_title;
    public string $protection_block_description;
    public string $protection_block_bg_color;
    public array $protection_block_points;
    public ?string $protection_block_title_color;
    public ?string $protection_block_title_size;
    public ?string $protection_block_title_weight;
    public ?string $protection_block_description_color;
    public ?string $protection_block_description_size;
    public ?string $protection_block_points_text_color;
    public ?string $protection_block_points_text_size;
    public ?string $protection_block_icon_color;

    // Recommended Services Block
    public bool $recommended_services_enabled = true;
    public string $recommended_services_title;
    public ?string $recommended_services_bg_color;
    public ?string $recommended_services_text_color;
    public ?string $recommended_services_heading_size;
    public ?string $recommended_services_heading_weight;
    public ?string $recommended_services_item_bg_color;
    public ?string $recommended_services_item_text_color;
    public ?string $recommended_services_item_desc_color;
    public ?string $recommended_services_item_icon_color;
    public ?string $recommended_services_item_icon_size;
    public array $recommended_services_items;

    public static function group(): string
    {
        return 'general';
    }
}
