<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'industry',
        'registration_number',
        'about',
        'logo',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
