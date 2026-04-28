<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'subtasks', 'category', 'icon'];

    protected $casts = [
        'subtasks' => 'array',
    ];

    public function providerServices()
    {
        return $this->hasMany(ProviderService::class);
    }
}
