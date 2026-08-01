<?php

namespace App\Repositories;

use App\Models\Divisi;
use App\Repositories\Contracts\DivisiRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentDivisiRepository implements DivisiRepositoryInterface
{
    public function getAllOrderedByName(): Collection
    {
        return Divisi::orderBy('nama_divisi')->get();
    }
}