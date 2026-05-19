<?php

namespace Modules\Emails\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Emails\Events\UserRegistered;
use Modules\Emails\Events\PasswordResetRequested;
use Modules\Emails\Events\ServiceRequested;
use Modules\Emails\Events\ProjectStatusUpdated;
use Modules\Emails\Events\InvoiceGenerated;
use Modules\Emails\Listeners\SendActivationEmail;
use Modules\Emails\Listeners\SendPasswordResetEmail;
use Modules\Emails\Listeners\SendServiceRequestEmails;
use Modules\Emails\Listeners\SendProjectUpdateEmail;
use Modules\Emails\Listeners\SendInvoiceEmail;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        UserRegistered::class => [
            SendActivationEmail::class,
        ],
        PasswordResetRequested::class => [
            SendPasswordResetEmail::class,
        ],
        ServiceRequested::class => [
            SendServiceRequestEmails::class,
        ],
        ProjectStatusUpdated::class => [
            SendProjectUpdateEmail::class,
        ],
        InvoiceGenerated::class => [
            SendInvoiceEmail::class,
        ],
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void {}
}
