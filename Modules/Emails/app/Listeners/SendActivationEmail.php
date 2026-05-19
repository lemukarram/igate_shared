<?php

namespace Modules\Emails\Listeners;

use Modules\Emails\Events\UserRegistered;
use Modules\Emails\Services\EmailService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendActivationEmail implements ShouldQueue
{
    protected EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function handle(UserRegistered $event): void
    {
        $activationLink = route('verify.email', ['token' => $event->activationToken]);
        
        $this->emailService->sendSignupActivation(
            $event->user->email,
            $event->user->name,
            $activationLink
        );
    }
}
