<?php

namespace App\Repositories;

use App\Models\Cuti;
use App\Repositories\Contracts\CutiRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentCutiRepository implements CutiRepositoryInterface
{
    public function __construct(
        private readonly Cuti $cuti
    ) {
    }

    public function paginateByPegawaiId(mixed $pegawaiId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->cuti
            ->newQuery()
            ->where('pegawai_id', $pegawaiId)
            ->latest()
            ->paginate($perPage);
    }

    public function calculateWorkingDayDuration(array $attributes): int
    {
        $temporaryCuti = $this->cuti->newInstance($attributes);

        return $temporaryCuti->durasi_hari_kerja;
    }

    public function create(array $attributes): Cuti
    {
        return $this->cuti
            ->newQuery()
            ->create($attributes);
    }
}