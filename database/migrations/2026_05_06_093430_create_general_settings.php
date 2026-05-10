<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', 'iGate');
        $this->migrator->add('general.default_currency', 'SAR');
        $this->migrator->add('general.platform_fee_percentage', 10.0);
        $this->migrator->add('general.default_language', 'en');
    }

    public function down(): void
    {
        $this->migrator->delete('general.site_name');
        $this->migrator->delete('general.default_currency');
        $this->migrator->delete('general.platform_fee_percentage');
        $this->migrator->delete('general.default_language');
    }
};
