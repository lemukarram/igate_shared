<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'company_id',
        'service_id',
        'provider_id',
        'plan_id',
        'plan_name',
        'billing_cycle',
        'status',
        'starts_at',
        'ends_at',
        'next_billing_date',
        'canceled_at',
        'card_token',
        'tap_customer_id',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'next_billing_date' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}
