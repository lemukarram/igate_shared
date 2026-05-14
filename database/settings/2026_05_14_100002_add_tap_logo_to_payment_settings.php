<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('payment.tap_logo', null);
    }

    public function down(): void
    {
        $this->migrator->delete('payment.tap_logo');
    }
};
