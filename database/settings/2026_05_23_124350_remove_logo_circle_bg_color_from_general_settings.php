<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->delete('general.logo_circle_bg_color');
    }

    public function down(): void
    {
        $this->migrator->add('general.logo_circle_bg_color', '#3da9e4');
    }
};
