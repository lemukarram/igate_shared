<?php

namespace Modules\Emails\Listeners;

use Modules\Emails\Events\PasswordResetRequested;
use Modules\Emails\Services\EmailService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPasswordResetEmail implements ShouldQueue
{
    protected EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function handle(PasswordResetRequested $event): void
    {
        $resetLink = route('password.reset', ['token' => $event->resetToken, 'email' => $event->user->email]);
        
        $this->emailService->sendForgotPassword(
            $event->user->email,
            $event->user->name,
            $resetLink
        );
    }
}
