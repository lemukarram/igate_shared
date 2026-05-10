<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.protection_block_title', 'iGate Protection Guarantee');
        $this->migrator->add('general.protection_block_description', 'Your peace of mind is our priority. Every transaction is covered by our comprehensive protection framework.');
        $this->migrator->add('general.protection_block_bg_color', '#f8fafc');
        $this->migrator->add('general.protection_block_points', [
            ['icon' => 'shield-check', 'text' => 'Guaranteed Payments'],
            ['icon' => 'clock', 'text' => 'SLA Protection'],
            ['icon' => 'lock', 'text' => 'Secure Documents'],
        ]);
    }

    public function down(): void
    {
        $this->migrator->delete('general.protection_block_title');
        $this->migrator->delete('general.protection_block_description');
        $this->migrator->delete('general.protection_block_bg_color');
        $this->migrator->delete('general.protection_block_points');
    }
};
