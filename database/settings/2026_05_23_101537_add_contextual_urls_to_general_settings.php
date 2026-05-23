<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.services_url', 'https://services.igate.com');
        $this->migrator->add('general.finance_url', 'https://finance.igate.com');
        $this->migrator->add('general.enterprise_url', 'https://enterprise.igate.com');
    }

    public function down(): void
    {
        $this->migrator->delete('general.services_url');
        $this->migrator->delete('general.finance_url');
        $this->migrator->delete('general.enterprise_url');
    }
};
