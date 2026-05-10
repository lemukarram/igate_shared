<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.logo', 'images/logo/logo.png');
        $this->migrator->add('general.collapsed_logo', 'images/logo/icon.png');
    }

    public function down(): void
    {
        $this->migrator->delete('general.logo');
        $this->migrator->delete('general.collapsed_logo');
    }
};
