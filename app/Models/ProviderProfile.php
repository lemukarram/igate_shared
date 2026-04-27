<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'commercial_registration',
        'tax_number',
        'bank_name',
        'iban',
        'bio',
        'logo',
        'status',
        'onboarding_completed',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
