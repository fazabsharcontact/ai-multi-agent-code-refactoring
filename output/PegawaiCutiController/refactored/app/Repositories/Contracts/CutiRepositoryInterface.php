<?php

namespace App\Repositories\Contracts;

use App\Models\Cuti;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CutiRepositoryInterface
{
    public function paginateByPegawaiId(mixed $pegawaiId, int $perPage = 10): LengthAwarePaginator;

    public function calculateWorkingDayDuration(array $attributes): int;

    public function create(array $attributes): Cuti;
}