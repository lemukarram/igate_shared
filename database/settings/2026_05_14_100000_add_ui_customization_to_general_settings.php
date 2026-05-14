<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Protection Block (Standardized Sidebar) Additional Styling
        $this->migrator->add('general.protection_block_title_color', '#9CA3AF');
        $this->migrator->add('general.protection_block_title_size', 'text-xs');
        $this->migrator->add('general.protection_block_title_weight', 'font-normal');
        $this->migrator->add('general.protection_block_description_color', '#D1D5DB');
        $this->migrator->add('general.protection_block_description_size', 'text-sm');
        $this->migrator->add('general.protection_block_points_text_color', '#FFFFFF');
        $this->migrator->add('general.protection_block_points_text_size', 'text-xs');
        $this->migrator->add('general.protection_block_icon_color', '#3B82F6');

        // Recommended Services Block
        $this->migrator->add('general.recommended_services_enabled', true);
        $this->migrator->add('general.recommended_services_title', 'Recommended Services');
        $this->migrator->add('general.recommended_services_bg_color', '#111827');
        $this->migrator->add('general.recommended_services_text_color', '#FFFFFF');
        $this->migrator->add('general.recommended_services_heading_size', 'text-xl');
        $this->migrator->add('general.recommended_services_heading_weight', 'font-normal');
        $this->migrator->add('general.recommended_services_item_bg_color', 'rgba(255, 255, 255, 0.05)');
        $this->migrator->add('general.recommended_services_item_text_color', '#3B82F6');
        $this->migrator->add('general.recommended_services_item_desc_color', '#9CA3AF');
        $this->migrator->add('general.recommended_services_item_icon_color', '#FFFFFF');
        $this->migrator->add('general.recommended_services_item_icon_size', '4');
        $this->migrator->add('general.recommended_services_items', [
            [
                'title' => 'ZATCA Compliance',
                'description' => 'Regulatory compliance solution for your business.',
                'icon' => 'shield-check',
                'link' => '#',
            ],
            [
                'title' => 'Legal Review',
                'description' => 'Professional legal document review and advisory.',
                'icon' => 'file-text',
                'link' => '#',
            ],
            [
                'title' => 'HR Management',
                'description' => 'Complete human resources and payroll management.',
                'icon' => 'users',
                'link' => '#',
            ],
        ]);
    }

    public function down(): void
    {
        $this->migrator->delete('general.protection_block_title_color');
        $this->migrator->delete('general.protection_block_title_size');
        $this->migrator->delete('general.protection_block_title_weight');
        $this->migrator->delete('general.protection_block_description_color');
        $this->migrator->delete('general.protection_block_description_size');
        $this->migrator->delete('general.protection_block_points_text_color');
        $this->migrator->delete('general.protection_block_points_text_size');
        $this->migrator->delete('general.protection_block_icon_color');

        $this->migrator->delete('general.recommended_services_enabled');
        $this->migrator->delete('general.recommended_services_title');
        $this->migrator->delete('general.recommended_services_bg_color');
        $this->migrator->delete('general.recommended_services_text_color');
        $this->migrator->delete('general.recommended_services_heading_size');
        $this->migrator->delete('general.recommended_services_heading_weight');
        $this->migrator->delete('general.recommended_services_item_bg_color');
        $this->migrator->delete('general.recommended_services_item_text_color');
        $this->migrator->delete('general.recommended_services_item_desc_color');
        $this->migrator->delete('general.recommended_services_item_icon_color');
        $this->migrator->delete('general.recommended_services_item_icon_size');
        $this->migrator->delete('general.recommended_services_items');
    }
};
