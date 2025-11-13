<?php

namespace App\Http\Interfaces;

use App\Models\Transaction;

interface ITransactionRepository
{
    public function createDepositTransaction(array $transactionData): Transaction;
    public function createRetraitTransaction(array $transactionData): Transaction;
    public function createPaymentTransaction(array $transactionData): Transaction;
    public function getTransactionsByComptePaginated(string $numeroCompte, ?string $typeTransaction = null, int $perPage = 15);
    public function performTransfer(string $compteEmetteurId, string $compteDestinataireId, float $montant): void;
    public function performPayment(string $compteEmetteurId, string $compteMarchandId, float $montant): void;
}