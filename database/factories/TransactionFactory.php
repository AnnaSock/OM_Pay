<?php

namespace Database\Factories;

use App\Enums\TypeTransaction;
use App\Models\Compte;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'montant' => $this->faker->randomFloat(2, 100, 10000),
            'type_transaction' => TypeTransaction::DEPOT, // Par défaut dépôt pour éviter les soldes négatifs
            'compte_id' => Compte::factory(),
        ];
    }

    /**
     * Indicate that the transaction is a depot.
     */
    public function depot(): static
    {
        return $this->state(fn (array $attributes) => [
            'type_transaction' => TypeTransaction::DEPOT,
            'montant' => $this->faker->randomFloat(2, 500, 5000), // Montants plus élevés pour les dépôts
        ]);
    }

    /**
     * Indicate that the transaction is a retrait.
     */
    public function retrait(): static
    {
        return $this->state(fn (array $attributes) => [
            'type_transaction' => TypeTransaction::RETRAIT,
            'montant' => $this->faker->randomFloat(2, 50, 1000), // Montants plus petits pour les retraits
        ]);
    }

    /**
     * Indicate that the transaction is a payment.
     */
    public function payement(): static
    {
        return $this->state(fn (array $attributes) => [
            'type_transaction' => TypeTransaction::PAYEMENT,
            'montant' => $this->faker->randomFloat(2, 50, 1000), // Montants plus petits pour les paiements
        ]);
    }

    /**
     * Create a transaction for a specific compte with balance check.
     */
    public function forCompte($compte, $maxMontant = null): static
    {
        return $this->state(function (array $attributes) use ($compte, $maxMontant) {
            $solde = $compte->getSolde();

            if ($maxMontant === null) {
                $maxMontant = $solde;
            }

            return [
                'compte_id' => $compte->id,
                'montant' => $this->faker->randomFloat(2, 10, min($maxMontant, 1000)),
            ];
        });
    }
}