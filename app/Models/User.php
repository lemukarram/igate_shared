<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'plan_id',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the provider profile associated with the user.
     */
    public function providerProfile()
    {
        return $this->hasOne(ProviderProfile::class);
    }

    public function companies()
    {
        return $this->hasMany(Company::class, 'client_id');
    }

    public function providerServices()
    {
        return $this->hasMany(ProviderService::class, 'provider_id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'client_id');
    }

    public function providerProjects()
    {
        return $this->hasMany(Project::class, 'provider_id');
    }

    public function ownedTeams()
    {
        return $this->hasMany(Team::class, 'owner_id');
    }

    public function teamMemberships()
    {
        return $this->hasMany(TeamMember::class, 'user_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
