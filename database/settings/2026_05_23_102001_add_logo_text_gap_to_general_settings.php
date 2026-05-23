<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.logo_text_gap', '1.6'); // 1.6 * 0.25rem = 0.4rem
    }

    public function down(): void
    {
        $this->migrator->delete('general.logo_text_gap');
    }
};
