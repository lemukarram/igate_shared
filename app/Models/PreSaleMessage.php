<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreSaleMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'provider_id',
        'service_id',
        'company_id',
        'sender_id',
        'message',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
