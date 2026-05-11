<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'subtasks', 'category', 'service_category_id', 'icon'];

    protected $casts = [
        'subtasks' => 'array',
    ];

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function providerServices()
    {
        return $this->hasMany(ProviderService::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function reviews()
    {
        return $this->hasManyThrough(Review::class, Project::class);
    }
}
