<?php

namespace App\Providers;

use App\Http\Interfaces\IClientRepository;
use App\Http\Interfaces\IRepository;
use App\Http\Interfaces\IUserRepository;
use App\Http\Interfaces\ICompteRepository;
use App\Http\Interfaces\ITransactionRepository;
use App\Http\Repositories\ClientRepository;
use App\Http\Repositories\TransactionRepository;
use App\Http\Repositories\UserRepository;
use App\Http\Repositories\CompteRepository;
use App\Http\Services\ClientService;
use App\Http\Services\TransactionService;
use App\Http\Services\CompteService;
use App\Models\Transaction;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(IRepository::class, function($app){
                return new TransactionRepository();
        });
        $this->app->singleton(TransactionService::class, function($app){
                $transactionRepo= $app->make(ITransactionRepository::class);
                return new TransactionService($transactionRepo);
        });

        $this->app->singleton(IClientRepository::class, function($app){
                return new ClientRepository();
        });

        $this->app->singleton(ClientService::class, function($app){
                $clientRepo= $app->make(IClientRepository::class);
                return new ClientService($clientRepo);
        });

        $this->app->singleton(IUserRepository::class, function($app){
                return new UserRepository();
        });

        $this->app->singleton(ICompteRepository::class, function($app){
                return new CompteRepository();
        });

        $this->app->singleton(ITransactionRepository::class, function($app){
                return new TransactionRepository();
        });

        $this->app->singleton(CompteService::class, function($app){
                $userRepo = $app->make(IUserRepository::class);
                $compteRepo = $app->make(ICompteRepository::class);
                $transactionRepo = $app->make(ITransactionRepository::class);
                return new CompteService($userRepo, $compteRepo, $transactionRepo);
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

        if (request()->header('X-Forwarded-Proto') == 'https') {
            URL::forceScheme('https');
        }
        
        Passport::setClientUuids(true);
    }
}
