<?php

namespace App\Repositories;

use App\Models\MasterTunjangan;
use App\Repositories\Contracts\MasterTunjanganRepositoryInterface;

class EloquentMasterTunjanganRepository implements MasterTunjanganRepositoryInterface
{
    public function create(array $data): MasterTunjangan
    {
        return MasterTunjangan::create($data);
    }

    public function update(MasterTunjangan $masterTunjangan, array $data): bool
    {
        return $masterTunjangan->update($data);
    }

    public function delete(MasterTunjangan $masterTunjangan): ?bool
    {
        return $masterTunjangan->delete();
    }
}