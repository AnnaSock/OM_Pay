<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\ITransactionRepository;
use App\Models\Transaction;
use App\Enums\TypeTransaction;

class TransactionRepository implements ITransactionRepository
{
    /**
     * Crée une transaction de dépôt
     *
     * @param array $transactionData
     * @return Transaction
     */
    public function createDepositTransaction(array $transactionData): Transaction
    {
        $transactionData['type_transaction'] = TypeTransaction::DEPOT;
        return Transaction::create($transactionData);
    }
}