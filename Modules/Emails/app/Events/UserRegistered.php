<?php

namespace Modules\Emails\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRegistered
{
    use Dispatchable, SerializesModels;

    public User $user;
    public string $activationToken;

    public function __construct(User $user, string $activationToken)
    {
        $this->user = $user;
        $this->activationToken = $activationToken;
    }
}
