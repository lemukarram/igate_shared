<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'provider_id',
        'team_id',
        'title',
        'description',
        'status',
        'order',
        'assigned_to',
        'due_date',
        'priority',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function histories()
    {
        return $this->hasMany(TaskHistory::class)->latest();
    }
}
