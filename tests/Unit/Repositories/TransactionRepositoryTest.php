<?php

namespace Tests\Unit\Repositories;

use App\Http\Interfaces\ITransactionRepository;
use App\Models\Client;
use App\Models\Compte;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ITransactionRepository $transactionRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transactionRepository = app(ITransactionRepository::class);
    }

    public function test_create_deposit_transaction_creates_deposit_transaction()
    {
        $user = Client::create([
            'nom' => 'Doe',
            'prenom' => 'John',
            'adresse' => '123 Main St',
            'nci' => '123456789',
            'email' => 'john.doe@example.com',
            'role' => 'Client',
        ]);

        $compte = Compte::create([
            'user_id' => $user->id,
            'user_type' => Client::class,
            'numero_compte' => 'ACC001',
            'numero_user' => 'USER001',
            'login' => 'login001',
            'password' => 'password123',
        ]);

        $transactionData = [
            'montant' => 1000.00,
            'compte_id' => $compte->id,
        ];

        $transaction = $this->transactionRepository->createDepositTransaction($transactionData);

        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals(1000.00, $transaction->montant);
        $this->assertEquals('Depot', $transaction->type_transaction->value);
        $this->assertEquals($compte->id, $transaction->compte_id);
        $this->assertDatabaseHas('transactions', [
            'montant' => 1000.00,
            'type_transaction' => 'Depot',
            'compte_id' => $compte->id,
        ]);
    }
}