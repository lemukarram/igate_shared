<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.logo_circle_padding_collapsed', '0.25rem');
        $this->migrator->add('general.logo_icon_size_collapsed', '1.5rem');
    }

    public function down(): void
    {
        $this->migrator->delete('general.logo_circle_padding_collapsed');
        $this->migrator->delete('general.logo_icon_size_collapsed');
    }
};
