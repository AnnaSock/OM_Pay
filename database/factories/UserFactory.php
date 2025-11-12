<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\Client;
use App\Models\Marchand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName(),
            'adresse' => $this->faker->address(),
            'nci' => $this->faker->unique()->numerify('##########'),
            'email' => $this->faker->unique()->safeEmail(),
            'role' => Role::CLIENT, // Par défaut client
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create a client user.
     */
    public function client(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => Role::CLIENT,
        ]);
    }

    /**
     * Create a marchand user.
     */
    public function marchand(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => Role::MARCHAND,
        ]);
    }
}
