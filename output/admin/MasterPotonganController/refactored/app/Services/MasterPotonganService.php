<?php

namespace App\Services;

use App\Models\MasterPotongan;
use App\Repositories\Contracts\MasterPotonganRepositoryInterface;

class MasterPotonganService
{
    public function __construct(
        private readonly MasterPotonganRepositoryInterface $masterPotonganRepository
    ) {
    }

    public function create(array $attributes): MasterPotongan
    {
        return $this->masterPotonganRepository->create($attributes);
    }

    public function update(MasterPotongan $masterPotongan, array $attributes): bool
    {
        return $this->masterPotonganRepository->update($masterPotongan, $attributes);
    }

    public function delete(MasterPotongan $masterPotongan): ?bool
    {
        return $this->masterPotonganRepository->delete($masterPotongan);
    }
}