<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Compte;
use App\Models\Marchand;
use App\Models\Transaction;
use App\Observers\ClientObserver;
use App\Observers\CompteObserver;
use App\Observers\MarchandObserver;
use App\Observers\TransactionObserver;
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
        Compte::observe(CompteObserver::class);
        Marchand::observe([MarchandObserver::class, UserObserver::class]);
        Transaction::observe(TransactionObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
