<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'event',
        'endpoint',
        'method',
        'payload',
        'response_body',
        'status_code',
        'ip_address',
    ];

    protected $casts = [
        'payload' => 'array',
        'response_body' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
