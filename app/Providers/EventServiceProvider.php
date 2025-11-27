<?php

namespace App\Providers;

use App\Events\AppointmentCreated;
use App\Events\AppointmentCancelled;
use App\Events\AppointmentCompleted;
use App\Listeners\SendAppointmentConfirmation;
use App\Listeners\SendAppointmentCancellationNotice;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        AppointmentCreated::class => [
            SendAppointmentConfirmation::class,
        ],
        AppointmentCancelled::class => [
            SendAppointmentCancellationNotice::class,
        ],
        AppointmentCompleted::class => [
            // Add listener for completion if needed
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
