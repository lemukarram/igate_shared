<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'name_ar', 'description', 'description_ar', 'subtasks', 'category', 'service_category_id', 'icon', 'is_active', 'protection_block'];

    protected $casts = [
        'subtasks' => 'array',
        'is_active' => 'boolean',
    ];

    public function getLocalizedNameAttribute()
    {
        return app()->getLocale() === 'ar' && $this->name_ar ? $this->name_ar : $this->name;
    }

    public function getLocalizedDescriptionAttribute()
    {
        return app()->getLocale() === 'ar' && $this->description_ar ? $this->description_ar : $this->description;
    }

    public function getTranslatedName(): string
    {
        return app()->getLocale() === 'ar' ? ($this->name_ar ?? $this->name) : $this->name;
    }

    public function category()
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
