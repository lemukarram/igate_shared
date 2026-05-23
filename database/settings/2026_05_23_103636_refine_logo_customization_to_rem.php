<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Update existing fields to use rem/numeric values
        $this->migrator->update('general.logo_circle_padding', fn() => '0.5rem');
        $this->migrator->update('general.logo_text_gap', fn() => '0.4rem');
        $this->migrator->update('general.logo_text_size', fn() => '1.25rem');
        $this->migrator->update('general.logo_text_weight', fn() => '600');

        // Add new field for icon size
        $this->migrator->add('general.logo_icon_size', '2rem');
    }

    public function down(): void
    {
        // Reverse if needed (keeping it simple)
        $this->migrator->delete('general.logo_icon_size');
    }
};
