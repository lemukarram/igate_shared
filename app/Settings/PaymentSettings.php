<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PaymentSettings extends Settings
{
    public ?string $environment;
    public ?string $sandbox_secret_key;
    public ?string $live_secret_key;
    public ?string $merchant_id;
    public ?string $webhook_secret;

    public static function group(): string
    {
        return 'payment';
    }
}
