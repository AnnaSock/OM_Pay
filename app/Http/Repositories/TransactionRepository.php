<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\IRepository;
use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


class TransactionRepository implements IRepository{

     protected $transaction;

     public function __construct(Transaction $transaction)
     {
         $this->transaction = $transaction;
     }

     public function all(array $filters = [], $sort = 'created_at', $order = 'desc', $limit = 10): LengthAwarePaginator{
         $query = $this->transaction->query();

         // Appliquer les filtres
         if (!empty($filters)) {
             foreach ($filters as $key => $value) {
                 if ($value !== null && $value !== '') {
                     $query->where($key, $value);
                 }
             }
         }

         // Appliquer le tri
         $query->orderBy($sort, $order);

         // Retourner la pagination
         return $query->paginate($limit);
     }
     public function find(int $id){}
     public function create(array $data){}
}