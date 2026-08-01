<?php

namespace App\Repositories\Contracts;

use App\Models\MasterTunjangan;

interface MasterTunjanganRepositoryInterface
{
    public function create(array $data): MasterTunjangan;

    public function update(MasterTunjangan $masterTunjangan, array $data): bool;

    public function delete(MasterTunjangan $masterTunjangan): ?bool;
}