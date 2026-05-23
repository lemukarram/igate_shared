<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.logo_circle_bg_color', '#FFFFFF');
        $this->migrator->add('general.logo_circle_padding', '2'); // as in p-2
        $this->migrator->add('general.logo_text_content', 'Services');
        $this->migrator->add('general.logo_text_color', '#111827');
        $this->migrator->add('general.logo_text_size', 'text-xl');
        $this->migrator->add('general.logo_text_weight', 'font-semibold');
    }

    public function down(): void
    {
        $this->migrator->delete('general.logo_circle_bg_color');
        $this->migrator->delete('general.logo_circle_padding');
        $this->migrator->delete('general.logo_text_content');
        $this->migrator->delete('general.logo_text_color');
        $this->migrator->delete('general.logo_text_size');
        $this->migrator->delete('general.logo_text_weight');
    }
};
