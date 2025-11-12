<?php

namespace Tests\Unit\Repositories;

use App\Http\Interfaces\ICompteRepository;
use App\Models\Client;
use App\Models\Compte;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompteRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ICompteRepository $compteRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->compteRepository = app(ICompteRepository::class);
    }

    public function test_compte_exists_for_user_returns_true_when_compte_exists()
    {
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

        $exists = $this->compteRepository->compteExistsForUser($user->id, 'ACC001');

        $this->assertTrue($exists);
    }

    public function test_compte_exists_for_user_returns_false_when_compte_does_not_exist()
    {
        $user = Client::create([
            'nom' => 'Doe',
            'prenom' => 'John',
            'adresse' => '123 Main St',
            'nci' => '123456789',
            'email' => 'john.doe@example.com',
            'role' => 'Client',
        ]);

        $exists = $this->compteRepository->compteExistsForUser($user->id, 'ACC001');

        $this->assertFalse($exists);
    }

    public function test_compte_exists_for_user_returns_false_for_different_user()
    {
        $user1 = Client::create([
            'nom' => 'Doe',
            'prenom' => 'John',
            'adresse' => '123 Main St',
            'nci' => '123456789',
            'email' => 'john.doe@example.com',
            'role' => 'Client',
        ]);

        $user2 = Client::create([
            'nom' => 'Smith',
            'prenom' => 'Jane',
            'adresse' => '456 Oak St',
            'nci' => '987654321',
            'email' => 'jane.smith@example.com',
            'role' => 'Client',
        ]);

        Compte::create([
            'user_id' => $user1->id,
            'user_type' => Client::class,
            'numero_compte' => 'ACC001',
            'numero_user' => 'USER001',
            'login' => 'login001',
            'password' => 'password123',
        ]);

        $exists = $this->compteRepository->compteExistsForUser($user2->id, 'ACC001');

        $this->assertFalse($exists);
    }

    public function test_create_compte_creates_new_compte()
    {
        $user = Client::create([
            'nom' => 'Doe',
            'prenom' => 'John',
            'adresse' => '123 Main St',
            'nci' => '123456789',
            'email' => 'john.doe@example.com',
            'role' => 'Client',
        ]);

        $compteData = [
            'user_id' => $user->id,
            'user_type' => Client::class,
            'numero_compte' => 'ACC001',
            'numero_user' => 'USER001',
            'login' => 'login001',
            'password' => 'password123',
        ];

        $compte = $this->compteRepository->createCompte($compteData);

        $this->assertInstanceOf(Compte::class, $compte);
        $this->assertEquals('ACC001', $compte->numero_compte);
        $this->assertDatabaseHas('comptes', ['numero_compte' => 'ACC001']);
    }
}