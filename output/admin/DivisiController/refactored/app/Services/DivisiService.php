<?php

namespace App\Services;

use App\Models\Divisi;
use App\Repositories\Contracts\DivisiRepositoryInterface;

class DivisiService
{
    public function __construct(
        private readonly DivisiRepositoryInterface $divisiRepository
    ) {
    }

    public function create(array $attributes): Divisi
    {
        return $this->divisiRepository->create($attributes);
    }

    public function update(Divisi $divisi, array $attributes): bool
    {
        return $this->divisiRepository->update($divisi, $attributes);
    }

    public function delete(Divisi $divisi): bool
    {
        if ($this->divisiRepository->countTeams($divisi) > 0) {
            return false;
        }

        $this->divisiRepository->delete($divisi);

        return true;
    }
}