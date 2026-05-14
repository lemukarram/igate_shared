<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class InvoiceSettings extends Settings
{
    public ?string $company_name;
    public ?string $logo;
    public ?string $address;
    public ?string $tax_id;
    public ?string $contact_info;
    public ?string $thank_you_note;
    public ?string $terms_conditions;
    public string $invoice_prefix;

    public static function group(): string
    {
        return 'invoice';
    }
}
