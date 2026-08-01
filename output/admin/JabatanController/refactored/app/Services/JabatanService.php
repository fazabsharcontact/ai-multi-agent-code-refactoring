<?php

namespace App\Services;

use App\Models\Jabatan;
use App\Repositories\Contracts\JabatanRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class JabatanService
{
    public function __construct(
        private readonly JabatanRepositoryInterface $jabatanRepository
    ) {
    }

    public function getFilteredJabatans(
        mixed $search = null,
        mixed $jabatanFilter = null
    ): Collection {
        return $this->jabatanRepository->getFiltered($search, $jabatanFilter);
    }

    public function createJabatan(array $data): Jabatan
    {
        return $this->jabatanRepository->create($data);
    }

    public function updateJabatan(Jabatan $jabatan, array $data): void
    {
        $this->jabatanRepository->update($jabatan, $data);
    }

    public function deleteJabatan(Jabatan $jabatan): void
    {
        $this->jabatanRepository->delete($jabatan);
    }
}