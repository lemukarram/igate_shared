<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.client_theme_color', '#3da9e4');
        $this->migrator->add('general.provider_theme_color', '#10b981'); // Example green for provider
    }

    public function down(): void
    {
        $this->migrator->delete('general.client_theme_color');
        $this->migrator->delete('general.provider_theme_color');
    }
};
