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

    public ?string $status_pending_label;
    public ?string $status_authorized_label;
    public ?string $status_captured_label;
    public ?string $status_failed_label;
    public ?string $status_refunded_label;
    public ?string $status_void_label;
    public ?string $status_cancelled_label;

    // Payment Logos
    public ?string $tap_logo;
    public ?string $visa_logo;
    public ?string $mastercard_logo;
    public ?string $mada_logo;

    public static function group(): string
    {
        return 'payment';
    }
}
