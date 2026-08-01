<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface PegawaiRepositoryInterface
{
    public function getAllOrderedByName(): Collection;
}