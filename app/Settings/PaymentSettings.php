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
    public ?string $success_title;
    public ?string $success_message;
    public int $auto_capture_days = 3;

    public static function group(): string
    {
        return 'payment';
    }
}
