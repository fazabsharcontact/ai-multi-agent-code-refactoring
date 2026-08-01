<?php

namespace App\Repositories;

use App\Models\Tim;
use App\Repositories\Contracts\TimRepositoryInterface;

class EloquentTimRepository implements TimRepositoryInterface
{
    public function create(array $data): Tim
    {
        return Tim::create($data);
    }

    public function update(Tim $tim, array $data): bool
    {
        return $tim->update($data);
    }

    public function hasPegawais(Tim $tim): bool
    {
        return $tim->pegawais()->count() > 0;
    }

    public function delete(Tim $tim): bool
    {
        return (bool) $tim->delete();
    }
}