<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('payment.visa_logo', null);
        $this->migrator->add('payment.mastercard_logo', null);
        $this->migrator->add('payment.mada_logo', null);
    }

    public function down(): void
    {
        $this->migrator->delete('payment.visa_logo');
        $this->migrator->delete('payment.mastercard_logo');
        $this->migrator->delete('payment.mada_logo');
    }
};
