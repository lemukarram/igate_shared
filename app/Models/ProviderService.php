<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderService extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'service_id',
        'price',
        'delivery_time_days',
        'provider_notes',
        'is_active',
    ];

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'service_id', 'service_id')
            ->whereColumn('projects.provider_id', 'provider_services.provider_id');
    }

    public function preSaleMessages()
    {
        return $this->hasMany(PreSaleMessage::class, 'service_id', 'service_id')
            ->whereColumn('pre_sale_messages.provider_id', 'provider_services.provider_id');
    }
}
