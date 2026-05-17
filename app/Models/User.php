<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Spatie\Permission\Traits\HasRoles;

use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

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
        'parent_id',
        'profile_picture',
        'notification_settings',
        'tap_customer_id',
        'card_token',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->hasRole(['Super Admin', 'Admin']) || $this->role === 'admin';
        }

        return true;
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function staff()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    public function getOwnedTeamAttribute()
    {
        return $this->ownedTeams()->first();
    }

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

    public function documents()
    {
        return $this->hasMany(Document::class, 'user_id');
    }

    public function preSaleMessages()
    {
        return $this->hasMany(PreSaleMessage::class, 'client_id');
    }

    public function teamMessages()
    {
        return $this->hasManyThrough(InternalMessage::class, Team::class, 'owner_id', 'team_id');
    }

    public function reviewsGiven()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getCompletedProjectsCountAttribute()
    {
        return $this->providerProjects()->where('status', 'completed')->count();
    }

    public function getActiveProjectsCountAttribute()
    {
        return $this->providerProjects()->where('status', 'active')->count();
    }

    public function getTotalClientsCountAttribute()
    {
        return $this->providerProjects()->distinct('client_id')->count('client_id');
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviewsReceived()->avg('rating') ?: 5.0;
    }

    public function getReviewCountAttribute()
    {
        return $this->reviewsReceived()->count();
    }

    public function enforcePlanLimits()
    {
        $plan = $this->plan;
        if (!$plan) return;

        // 1. Projects
        $activeProjects = $this->role === 'client' 
            ? $this->projects()->where('status', 'active')->orderBy('created_at', 'desc')->get()
            : $this->providerProjects()->where('status', 'active')->orderBy('created_at', 'desc')->get();

        if ($activeProjects->count() > $plan->max_projects) {
            $projectsToDeactivate = $activeProjects->slice($plan->max_projects);
            foreach ($projectsToDeactivate as $project) {
                $project->update(['status' => 'inactive']);
            }
        }

        // 2. Companies (Client only)
        if ($this->role === 'client') {
            $activeCompanies = $this->companies()->where('is_active', true)->orderBy('created_at', 'desc')->get();
            if ($activeCompanies->count() > $plan->max_companies) {
                $companiesToDeactivate = $activeCompanies->slice($plan->max_companies);
                foreach ($companiesToDeactivate as $company) {
                    $company->update(['is_active' => false]);
                }
            }
        }

        // 3. Services (Provider only)
        if ($this->role === 'provider') {
            $activeServices = $this->providerServices()->where('is_active', true)->orderBy('created_at', 'desc')->get();
            if ($activeServices->count() > $plan->max_services) {
                $servicesToDeactivate = $activeServices->slice($plan->max_services);
                foreach ($servicesToDeactivate as $service) {
                    $service->update(['is_active' => false]);
                }
            }
        }

        // 4. Team Members
        $team = $this->ownedTeam;
        if ($team) {
            $activeMembers = $team->members()->where('is_active', true)->orderBy('created_at', 'desc')->get();
            if ($activeMembers->count() > $plan->max_users) {
                $membersToDeactivate = $activeMembers->slice($plan->max_users);
                foreach ($membersToDeactivate as $member) {
                    $member->update(['is_active' => false]);
                }
            }
        }
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
            'notification_settings' => 'array',
        ];
    }
}
