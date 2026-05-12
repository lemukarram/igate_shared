<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('payment.environment', 'sandbox');
        $this->migrator->add('payment.sandbox_secret_key', '');
        $this->migrator->add('payment.live_secret_key', '');
        $this->migrator->add('payment.merchant_id', '');
        $this->migrator->add('payment.webhook_secret', '');
    }
    
    public function down(): void
    {
        $this->migrator->delete('payment.environment');
        $this->migrator->delete('payment.sandbox_secret_key');
        $this->migrator->delete('payment.live_secret_key');
        $this->migrator->delete('payment.merchant_id');
        $this->migrator->delete('payment.webhook_secret');
    }
};
