<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Marchand;
use App\Observers\ClientObserver;
use App\Observers\MarchandObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

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

        \App\Events\OtpRequested::class => [
            \App\Listeners\SendOtpSms::class,
        ],

        \App\Events\OtpVerified::class => [
            \App\Listeners\SendCredentialsSms::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Client::observe([ClientObserver::class, UserObserver::class]);
        Marchand::observe([MarchandObserver::class, UserObserver::class]);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
