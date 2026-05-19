<?php

namespace Modules\Emails\Listeners;

use Modules\Emails\Events\ServiceRequested;
use Modules\Emails\Services\EmailService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendServiceRequestEmails implements ShouldQueue
{
    protected EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function handle(ServiceRequested $event): void
    {
        $project = $event->project;
        $serviceName = $project->service->name ?? 'Standardized Service';
        
        // 1. Notify the Client (who made the request)
        $client = $project->client ?? $project->company->users()->first() ?? null;
        if ($client) {
            $this->emailService->sendNewServiceRequest(
                $client->email,
                $client->name,
                $serviceName,
                $project->name ?? 'Service Request #' . $project->id
            );
        }

        // 2. Notify the Provider (who will fulfill the request)
        $provider = $project->provider ?? ($project->providerProfile->user ?? ($project->providerProfile->team->users()->first() ?? null));
        if ($provider) {
            $this->emailService->sendNewServiceRequest(
                $provider->email,
                $provider->name,
                $serviceName,
                $project->name ?? 'Service Request #' . $project->id
            );
        }
    }
}
