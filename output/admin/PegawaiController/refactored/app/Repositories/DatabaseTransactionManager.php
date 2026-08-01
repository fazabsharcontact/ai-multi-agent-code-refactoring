<?php

namespace App\Repositories;

use App\Repositories\Contracts\TransactionManagerInterface;
use Closure;
use Illuminate\Support\Facades\DB;

class DatabaseTransactionManager implements TransactionManagerInterface
{
    public function transaction(Closure $callback): mixed
    {
        return DB::transaction($callback);
    }
}