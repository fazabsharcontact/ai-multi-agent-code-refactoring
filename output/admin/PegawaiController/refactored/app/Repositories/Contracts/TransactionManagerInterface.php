<?php

namespace App\Repositories\Contracts;

use Closure;

interface TransactionManagerInterface
{
    public function transaction(Closure $callback): mixed;
}