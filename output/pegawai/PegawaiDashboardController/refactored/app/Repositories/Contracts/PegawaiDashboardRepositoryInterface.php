<?php

namespace App\Repositories\Contracts;

use App\Models\Pegawai;
use Illuminate\Support\Collection;

interface PegawaiDashboardRepositoryInterface
{
    public function findPegawaiByUserId(mixed $userId): ?Pegawai;

    public function countKehadiranByStatus(
        mixed $pegawaiId,
        string $status,
        int $bulan,
        int $tahun
    ): int;

    public function getTotalGajiTahunan(mixed $pegawaiId, int $tahun): mixed;

    public function getGajiPerBulan(mixed $pegawaiId, int $tahun): array;

    public function getPengumumanForPegawai(Pegawai $pegawai): Collection;
}