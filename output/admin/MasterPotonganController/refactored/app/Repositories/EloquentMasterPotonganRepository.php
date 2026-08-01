<?php

namespace App\Repositories;

use App\Models\MasterPotongan;
use App\Repositories\Contracts\MasterPotonganRepositoryInterface;

class EloquentMasterPotonganRepository implements MasterPotonganRepositoryInterface
{
    public function create(array $attributes): MasterPotongan
    {
        return MasterPotongan::create($attributes);
    }

    public function update(MasterPotongan $masterPotongan, array $attributes): bool
    {
        return $masterPotongan->update($attributes);
    }

    public function delete(MasterPotongan $masterPotongan): ?bool
    {
        return $masterPotongan->delete();
    }
}