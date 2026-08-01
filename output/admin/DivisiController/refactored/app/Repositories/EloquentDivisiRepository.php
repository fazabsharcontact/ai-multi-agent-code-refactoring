<?php

namespace App\Repositories;

use App\Models\Divisi;
use App\Repositories\Contracts\DivisiRepositoryInterface;

class EloquentDivisiRepository implements DivisiRepositoryInterface
{
    public function create(array $attributes): Divisi
    {
        return Divisi::create($attributes);
    }

    public function update(Divisi $divisi, array $attributes): bool
    {
        return $divisi->update($attributes);
    }

    public function countTeams(Divisi $divisi): int
    {
        return $divisi->tims()->count();
    }

    public function delete(Divisi $divisi): bool
    {
        return $divisi->delete();
    }
}