<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('payment.success_title', 'Payment Successful!');
        $this->migrator->add('payment.success_message', 'Your transaction has been confirmed. Our team is now reviewing the request. Once approved, your project workspace will become active shortly.');
        $this->migrator->add('payment.auto_capture_days', 3);
    }
};
