<?php

namespace App\Services;

use App\Models\MasterTunjangan;
use App\Repositories\Contracts\MasterTunjanganRepositoryInterface;

class MasterTunjanganService
{
    public function __construct(
        private readonly MasterTunjanganRepositoryInterface $masterTunjanganRepository
    ) {
    }

    public function create(array $data): MasterTunjangan
    {
        return $this->masterTunjanganRepository->create($data);
    }

    public function update(MasterTunjangan $masterTunjangan, array $data): bool
    {
        return $this->masterTunjanganRepository->update($masterTunjangan, $data);
    }

    public function delete(MasterTunjangan $masterTunjangan): ?bool
    {
        return $this->masterTunjanganRepository->delete($masterTunjangan);
    }
}