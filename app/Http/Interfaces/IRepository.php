<?php

namespace App\Http\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface IRepository
{
    public function all(array $filters = [], $sort = 'created_at', $order = 'desc', $limit = 10): LengthAwarePaginator;
    public function find(int $id);
    public function create(array $data);
}
