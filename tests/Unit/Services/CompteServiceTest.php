<?php

namespace Tests\Unit\Services;

use App\Http\Services\CompteService;
use App\Models\Client;
use App\Models\Compte;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompteServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CompteService $compteService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->compteService = app(CompteService::class);
    }

    public function test_create_user_compte_with_initial_deposit_creates_user_compte_and_transaction()
    {
        $userData = [
            'nom' => 'Doe',
            'prenom' => 'John',
            'adresse' => '123 Main St',
            'nci' => '123456789',
            'email' => 'john.doe@example.com',
            'numero_user' => 'USER001',
            'login' => 'login001',
            'password' => 'password123',
        ];

        $accountNumber = 'ACC001';
        $initialDeposit = 1000.00;

        $compte = $this->compteService->createUserCompteWithInitialDeposit(
            $userData,
            $accountNumber,
            $initialDeposit
        );

        $this->assertInstanceOf(Compte::class, $compte);
        $this->assertEquals($accountNumber, $compte->numero_compte);

        // Vérifier que l'utilisateur a été créé
        $this->assertDatabaseHas('users', [
            'nom' => 'Doe',
            'prenom' => 'John',
            'email' => 'john.doe@example.com',
        ]);

        // Vérifier que le compte a été créé
        $this->assertDatabaseHas('comptes', [
            'numero_compte' => $accountNumber,
            'numero_user' => 'USER001',
            'login' => 'login001',
        ]);

        // Vérifier que la transaction de dépôt a été créée
        $this->assertDatabaseHas('transactions', [
            'montant' => $initialDeposit,
            'type_transaction' => 'Depot',
            'compte_id' => $compte->id,
        ]);

        // Vérifier que le solde du compte est correct
        $compte->refresh();
        $this->assertEquals($initialDeposit, $compte->getSolde());
    }

    public function test_create_user_compte_with_initial_deposit_uses_existing_user()
    {
        // Créer un utilisateur existant
        $existingUser = Client::create([
            'nom' => 'Doe',
            'prenom' => 'John',
            'adresse' => '123 Main St',
            'nci' => '123456789',
            'email' => 'john.doe@example.com',
            'role' => 'Client',
        ]);

        $userData = [
            'nom' => 'Smith', // Différent
            'prenom' => 'Jane', // Différent
            'adresse' => '456 Oak St', // Différent
            'nci' => '987654321', // Différent
            'email' => 'john.doe@example.com', // Même email
            'numero_user' => 'USER002',
            'login' => 'login002',
            'password' => 'password456',
        ];

        $accountNumber = 'ACC002';
        $initialDeposit = 500.00;

        $compte = $this->compteService->createUserCompteWithInitialDeposit(
            $userData,
            $accountNumber,
            $initialDeposit
        );

        // Vérifier que c'est le même utilisateur
        $this->assertEquals($existingUser->id, $compte->user_id);

        // Vérifier que le compte a été créé avec les nouvelles données
        $this->assertEquals($accountNumber, $compte->numero_compte);
        $this->assertEquals('USER002', $compte->numero_user);
    }

    public function test_create_user_compte_with_initial_deposit_throws_exception_for_duplicate_account_number()
    {
        // Créer un utilisateur et un compte existant
        $user = Client::create([
            'nom' => 'Doe',
            'prenom' => 'John',
            'adresse' => '123 Main St',
            'nci' => '123456789',
            'email' => 'john.doe@example.com',
            'role' => 'Client',
        ]);

        Compte::create([
            'user_id' => $user->id,
            'user_type' => Client::class,
            'numero_compte' => 'ACC001',
            'numero_user' => 'USER001',
            'login' => 'login001',
            'password' => 'password123',
        ]);

        $userData = [
            'nom' => 'Doe',
            'prenom' => 'John',
            'adresse' => '123 Main St',
            'nci' => '123456789',
            'email' => 'john.doe@example.com',
            'numero_user' => 'USER001',
            'login' => 'login001',
            'password' => 'password123',
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Un compte avec ce numéro existe déjà pour cet utilisateur');

        $this->compteService->createUserCompteWithInitialDeposit(
            $userData,
            'ACC001', // Même numéro de compte
            1000.00
        );
    }
}