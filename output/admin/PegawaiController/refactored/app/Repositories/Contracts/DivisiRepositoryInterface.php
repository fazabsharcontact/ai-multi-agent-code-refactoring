<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface DivisiRepositoryInterface
{
    public function getAllWithTims(): Collection;
}