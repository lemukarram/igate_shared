<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('payment.status_pending_label', 'Pending');
        $this->migrator->add('payment.status_authorized_label', 'Authorized (Escrow)');
        $this->migrator->add('payment.status_captured_label', 'Captured');
        $this->migrator->add('payment.status_failed_label', 'Failed / Declined');
        $this->migrator->add('payment.status_refunded_label', 'Refunded');
        $this->migrator->add('payment.status_void_label', 'Voided');
        $this->migrator->add('payment.status_cancelled_label', 'Cancelled');
    }

    public function down(): void
    {
        $this->migrator->delete('payment.status_pending_label');
        $this->migrator->delete('payment.status_authorized_label');
        $this->migrator->delete('payment.status_captured_label');
        $this->migrator->delete('payment.status_failed_label');
        $this->migrator->delete('payment.status_refunded_label');
        $this->migrator->delete('payment.status_void_label');
        $this->migrator->delete('payment.status_cancelled_label');
    }
};
