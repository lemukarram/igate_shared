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

    public function documents()
    {
        return $this->hasMany(Document::class, 'user_id', 'user_id');
    }

    public function providerServices()
    {
        return $this->hasMany(ProviderService::class, 'provider_id', 'user_id');
    }

    public function teamMembers()
    {
        return $this->hasManyThrough(
            TeamMember::class,
            Team::class,
            'owner_id',
            'team_id',
            'user_id',
            'id'
        );
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'provider_id', 'user_id');
    }

    public function preSaleMessages()
    {
        return $this->hasMany(PreSaleMessage::class, 'provider_id', 'user_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'provider_id', 'user_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'reviewee_id', 'user_id');
    }

    public function getAboutAttribute()
    {
        return $this->bio;
    }
}
