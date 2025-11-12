<?php

namespace Tests\Unit\Repositories;

use App\Http\Interfaces\IUserRepository;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected IUserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = app(IUserRepository::class);
    }

    public function test_first_or_create_user_creates_new_user()
    {
        $userData = [
            'nom' => 'Doe',
            'prenom' => 'John',
            'adresse' => '123 Main St',
            'nci' => '123456789',
            'email' => 'john.doe@example.com',
        ];

        $user = $this->userRepository->firstOrCreateUser($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Doe', $user->nom);
        $this->assertEquals('john.doe@example.com', $user->email);
        $this->assertDatabaseHas('users', ['email' => 'john.doe@example.com']);
    }

    public function test_first_or_create_user_returns_existing_user()
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
            'nom' => 'Smith', // Différent nom
            'prenom' => 'Jane', // Différent prénom
            'adresse' => '456 Oak St', // Différente adresse
            'nci' => '987654321', // Différent NCI
            'email' => 'john.doe@example.com', // Même email
        ];

        $user = $this->userRepository->firstOrCreateUser($userData);

        $this->assertEquals($existingUser->id, $user->id);
        $this->assertEquals('Doe', $user->nom); // Devrait garder les données existantes
    }

    public function test_user_exists_returns_true_for_existing_user()
    {
        $user = Client::create([
            'nom' => 'Doe',
            'prenom' => 'John',
            'adresse' => '123 Main St',
            'nci' => '123456789',
            'email' => 'john.doe@example.com',
            'role' => 'Client',
        ]);

        $exists = $this->userRepository->userExists($user->id);

        $this->assertTrue($exists);
    }

    public function test_user_exists_returns_false_for_non_existing_user()
    {
        $exists = $this->userRepository->userExists('non-existing-id');

        $this->assertFalse($exists);
    }
}