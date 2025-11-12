<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Compte;
use App\Models\Marchand;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Compte>
 */
class CompteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Compte::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'numero_compte' => $this->faker->unique()->numerify('##########'),
            'numero_user' => $this->faker->unique()->numerify('77#######'),
            'login' => $this->generateUniqueLogin(),
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ];
    }

    /**
     * Create a compte for a specific user.
     */
    public function forUser($user): static
    {
        return $this->state(function (array $attributes) use ($user) {
            return [
                'user_id' => $user->id,
                'user_type' => get_class($user),
                'code_marchand' => $user instanceof Marchand ? $this->faker->unique()->numerify('M#######') : null,
            ];
        });
    }

    private function generateUniqueLogin(): string
    {
        do {
            $login = 'USER' . str_pad(mt_rand(10000, 99999), 5, '0', STR_PAD_LEFT);
        } while (Compte::where('login', $login)->exists());

        return $login;
    }
}