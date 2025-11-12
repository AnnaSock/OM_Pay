<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Marchand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Marchand>
 */
class MarchandFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Marchand::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName(),
            'adresse' => $this->faker->address(),
            'nci' => $this->faker->unique()->numerify('##########'),
            'email' => $this->faker->unique()->safeEmail(),
            'role' => \App\Enums\Role::MARCHAND,
        ];
    }

    private function generateUniqueLogin(): string
    {
        do {
            $login = 'USER' . str_pad(mt_rand(10000, 99999), 5, '0', STR_PAD_LEFT);
        } while (\App\Models\Compte::where('login', $login)->exists());

        return $login;
    }
}