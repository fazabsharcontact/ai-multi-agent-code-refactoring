<?php

namespace App\Repositories\Contracts;

use App\Models\Tugas;
use App\Models\TugasPengumpulan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TugasPengumpulanRepositoryInterface
{
    public function paginateWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function getAllJabatansOrderedByName(): Collection;

    public function getAllTimsOrderedByName(): Collection;

    public function getAllDivisisOrderedByName(): Collection;

    public function findOrFail(mixed $id): TugasPengumpulan;

    public function saveTugasPengumpulan(TugasPengumpulan $tugasPengumpulan): bool;

    public function saveTugas(Tugas $tugas): bool;
}