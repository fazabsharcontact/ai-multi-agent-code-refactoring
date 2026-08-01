<?php

namespace App\Repositories\Contracts;

use App\Models\Kehadiran;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface KehadiranRepositoryInterface
{
    public function paginateByPegawaiAndPeriod(
        int $pegawaiId,
        mixed $tahun,
        mixed $bulan,
        int $perPage = 10
    ): LengthAwarePaginator;

    public function findByPegawaiAndDate(
        int $pegawaiId,
        string $tanggal
    ): ?Kehadiran;

    public function create(array $data): Kehadiran;

    public function updateJamPulang(
        Kehadiran $kehadiran,
        string $jamPulang
    ): bool;
}