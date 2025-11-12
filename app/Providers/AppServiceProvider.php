<?php

namespace App\Providers;

use App\Http\Interfaces\IClientRepository;
use App\Http\Interfaces\IRepository;
use App\Http\Repositories\ClientRepository;
use App\Http\Repositories\TransactionRepository;
use App\Http\Services\ClientService;
use App\Http\Services\TransactionService;
use App\Models\Client;
use App\Models\Marchand;
use App\Models\Transaction;
use App\Models\User;
use App\Observers\ClientObserver;
use App\Observers\MarchandObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(IRepository::class, function($app){
                return new TransactionRepository(new Transaction());
        });
        $this->app->singleton(TransactionService::class, function($app){
                $transactionRepo= $app->make(IRepository::class);
                return new TransactionService($transactionRepo);
        });

        $this->app->singleton(IClientRepository::class, function($app){
                return new ClientRepository();
        });

        $this->app->singleton(ClientService::class, function($app){
                $clientRepo= $app->make(IClientRepository::class);
                return new ClientService($clientRepo);
        });
       
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    
    {

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Client::observe([ClientObserver::class, UserObserver::class]);
        Marchand::observe([MarchandObserver::class, UserObserver::class]);

        // Forcer Passport à utiliser les UUIDs pour les clients
        \Laravel\Passport\Passport::setClientUuids(true);
    }
}
