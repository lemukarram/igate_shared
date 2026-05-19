<?php

namespace Modules\Emails\Listeners;

use Modules\Emails\Events\ProjectStatusUpdated;
use Modules\Emails\Services\EmailService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendProjectUpdateEmail implements ShouldQueue
{
    protected EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function handle(ProjectStatusUpdated $event): void
    {
        $project = $event->project;
        $client = $project->client ?? $project->company->users()->first() ?? null;

        if ($client) {
            $this->emailService->sendProjectStatusUpdate(
                $client->email,
                $client->name,
                $project->name,
                $event->newStatus
            );
        }
    }
}
