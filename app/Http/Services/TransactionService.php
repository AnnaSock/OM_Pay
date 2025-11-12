<?php

namespace App\Http\Services;

use App\Http\Interfaces\IRepository;
use App\Http\Repositories\TransactionRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TransactionService{

    protected $transactionRepository;

    public function __construct(IRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function getAllTransactions(array $filters = [], $sort = 'created_at', $order = 'desc', $limit = 10): LengthAwarePaginator
    {
        return $this->transactionRepository->all($filters, $sort, $order, $limit);
    }
}