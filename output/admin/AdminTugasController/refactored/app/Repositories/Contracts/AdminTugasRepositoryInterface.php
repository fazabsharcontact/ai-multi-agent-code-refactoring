<?php

namespace App\Repositories\Contracts;

use App\Models\Tugas;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface AdminTugasRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function getPegawais(): Collection;

    public function getJabatans(): Collection;

    public function getTims(): Collection;

    public function getDivisis(): Collection;

    public function create(array $data): Tugas;
}