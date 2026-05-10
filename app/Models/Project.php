<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'provider_id',
        'service_id',
        'company_id',
        'status',
        'total_amount',
        'start_date',
        'end_date',
        'completed_at',
        'client_notified_at',
        'escrow_released_at',
        'dispute_reason',
        'termination_reason',
        'provider_marked_complete',
        'client_approved',
        'mutual_cancellation_requested',
        'cancellation_requested_by',
        'termination_requested',
        'termination_requested_at',
        'rejection_reason',
        'rejected_at',
        'last_action_by',
    ];

    public function histories()
    {
        return $this->hasMany(ProjectHistory::class)->latest();
    }

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

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function releaseRequests()
    {
        return $this->hasMany(ReleaseRequest::class);
    }

    public function providerService()
    {
        return $this->belongsTo(ProviderService::class);
    }
}
