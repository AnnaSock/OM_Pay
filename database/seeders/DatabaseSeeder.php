<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Compte;
use App\Models\Marchand;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer le client d'accès personnel pour Passport
        if (\Laravel\Passport\Client::where('personal_access_client', true)->doesntExist()) {
            $client = \Laravel\Passport\Client::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'user_id' => null,
                'name' => 'OM Pay Personal Access Client',
                'secret' => \Illuminate\Support\Str::random(40),
                'provider' => null,
                'redirect' => 'http://localhost',
                'personal_access_client' => true,
                'password_client' => false,
                'revoked' => false,
            ]);

            // Créer l'entrée dans oauth_personal_access_clients
            \Laravel\Passport\PersonalAccessClient::create([
                'client_id' => $client->id,
            ]);
        }

        // Créer 2 marchands avec chacun un compte
        Marchand::factory(2)->create()->each(function ($marchand) {
            Compte::factory()->forUser($marchand)->create();
        });

        // Créer 3 clients avec chacun un compte et des transactions respectant le solde
        Client::factory(3)->create()->each(function ($client) {
            $compte = Compte::factory()->forUser($client)->create();

            // Créer d'abord des dépôts
            $depots = Transaction::factory(rand(3, 5))->depot()->create(['compte_id' => $compte->id]);

            // Calculer le solde total des dépôts
            $soldeTotal = $depots->sum('montant');

            // Créer des retraits et paiements ne dépassant pas le solde
            if ($soldeTotal > 0) {
                $retraits = Transaction::factory(rand(1, 3))->retrait()->create([
                    'compte_id' => $compte->id,
                    'montant' => function () use (&$soldeTotal) {
                        $montant = min(fake()->randomFloat(2, 50, min(1000, $soldeTotal)), $soldeTotal);
                        $soldeTotal -= $montant;
                        return $montant;
                    }
                ]);

                if ($soldeTotal > 0) {
                    $paiements = Transaction::factory(rand(1, 3))->payement()->create([
                        'compte_id' => $compte->id,
                        'montant' => function () use (&$soldeTotal) {
                            $montant = min(fake()->randomFloat(2, 50, min(1000, $soldeTotal)), $soldeTotal);
                            $soldeTotal -= $montant;
                            return $montant;
                        }
                    ]);
                }
            }
        });
    }
}
