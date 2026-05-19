<?php

namespace Modules\Emails\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailTemplate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'event_key',
        'is_active',
        'subject',
        'body',
        'variables',
    ];
}
