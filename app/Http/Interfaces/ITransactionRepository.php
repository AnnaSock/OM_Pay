<?php

namespace App\Http\Interfaces;

use App\Models\Transaction;

interface ITransactionRepository
{
    public function createDepositTransaction(array $transactionData): Transaction;
}