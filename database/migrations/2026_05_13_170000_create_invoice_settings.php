<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('invoice.logo', null);
        $this->migrator->add('invoice.address', 'Riyadh, Saudi Arabia');
        $this->migrator->add('invoice.tax_id', '123456789');
        $this->migrator->add('invoice.contact_info', 'contact@igate.sa');
        $this->migrator->add('invoice.thank_you_note', 'Thank you for choosing iGate!');
        $this->migrator->add('invoice.terms_conditions', 'Terms and conditions apply.');
        $this->migrator->add('invoice.invoice_prefix', 'IGATE-');
    }

    public function down(): void
    {
        $this->migrator->delete('invoice.logo');
        $this->migrator->delete('invoice.address');
        $this->migrator->delete('invoice.tax_id');
        $this->migrator->delete('invoice.contact_info');
        $this->migrator->delete('invoice.thank_you_note');
        $this->migrator->delete('invoice.terms_conditions');
        $this->migrator->delete('invoice.invoice_prefix');
    }
};
