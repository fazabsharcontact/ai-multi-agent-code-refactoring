<?php

namespace App\Services;

use App\Models\Tim;
use App\Repositories\Contracts\DivisiRepositoryInterface;
use App\Repositories\Contracts\TimRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TimService
{
    public function __construct(
        private readonly TimRepositoryInterface $timRepository,
        private readonly DivisiRepositoryInterface $divisiRepository
    ) {
    }

    public function getDivisis(): Collection
    {
        return $this->divisiRepository->getAllOrderedByName();
    }

    public function createTim(array $data): Tim
    {
        return $this->timRepository->create($data);
    }

    public function updateTim(Tim $tim, array $data): bool
    {
        return $this->timRepository->update($tim, $data);
    }

    public function deleteTim(Tim $tim): bool
    {
        if ($this->timRepository->hasPegawais($tim)) {
            return false;
        }

        $this->timRepository->delete($tim);

        return true;
    }
}