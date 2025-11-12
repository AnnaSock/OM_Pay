<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompteApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_compte_successfully()
    {
        $payload = [
            'nom' => 'Doe',
            'prenom' => 'John',
            'adresse' => '123 Main St',
            'nci' => '123456789',
            'email' => 'john.doe@example.com',
            'numero_user' => 'USER001',
            'numero_compte' => 'ACC001',
            'login' => 'login001',
            'password' => 'password123',
            'montant_initial' => 1000.00,
        ];

        $response = $this->postJson('/api/comptes', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Compte créé avec succès',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'compte' => [
                        'id',
                        'numero_compte',
                        'numero_user',
                        'login',
                        'solde',
                        'user',
                    ],
                    'transaction_depot' => [
                        'id',
                        'montant',
                        'type_transaction',
                        'compte_id',
                    ],
                ],
            ]);

        // Vérifier que les données ont été créées en base
        $this->assertDatabaseHas('users', [
            'nom' => 'Doe',
            'prenom' => 'John',
            'email' => 'john.doe@example.com',
        ]);

        $this->assertDatabaseHas('comptes', [
            'numero_compte' => 'ACC001',
            'numero_user' => 'USER001',
            'login' => 'login001',
        ]);

        $this->assertDatabaseHas('transactions', [
            'montant' => 1000.00,
            'type_transaction' => 'Depot',
        ]);
    }

    public function test_create_compte_validation_errors()
    {
        $payload = [
            // Données manquantes
        ];

        $response = $this->postJson('/api/comptes', $payload);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Erreur de validation',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'errors',
            ]);
    }

    public function test_create_compte_duplicate_email()
    {
        // Créer un utilisateur existant
        \App\Models\Client::create([
            'nom' => 'Existing',
            'prenom' => 'User',
            'adresse' => '123 Main St',
            'nci' => '123456789',
            'email' => 'john.doe@example.com',
            'role' => 'Client',
        ]);

        $payload = [
            'nom' => 'Doe',
            'prenom' => 'John',
            'adresse' => '123 Main St',
            'nci' => '987654321',
            'email' => 'john.doe@example.com', // Email déjà utilisé
            'numero_user' => 'USER001',
            'numero_compte' => 'ACC001',
            'login' => 'login001',
            'password' => 'password123',
            'montant_initial' => 1000.00,
        ];

        $response = $this->postJson('/api/comptes', $payload);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Erreur de validation',
            ]);
    }

    public function test_create_compte_duplicate_account_number()
    {
        // Créer un compte existant
        $user = \App\Models\Client::create([
            'nom' => 'Existing',
            'prenom' => 'User',
            'adresse' => '123 Main St',
            'nci' => '123456789',
            'email' => 'existing@example.com',
            'role' => 'Client',
        ]);

        \App\Models\Compte::create([
            'user_id' => $user->id,
            'user_type' => \App\Models\Client::class,
            'numero_compte' => 'ACC001',
            'numero_user' => 'USER001',
            'login' => 'login001',
            'password' => 'password123',
        ]);

        $payload = [
            'nom' => 'Doe',
            'prenom' => 'John',
            'adresse' => '456 Oak St',
            'nci' => '987654321',
            'email' => 'john.doe@example.com',
            'numero_user' => 'USER002',
            'numero_compte' => 'ACC001', // Numéro de compte déjà utilisé
            'login' => 'login002',
            'password' => 'password456',
            'montant_initial' => 1000.00,
        ];

        $response = $this->postJson('/api/comptes', $payload);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Erreur de validation',
            ]);
    }
}