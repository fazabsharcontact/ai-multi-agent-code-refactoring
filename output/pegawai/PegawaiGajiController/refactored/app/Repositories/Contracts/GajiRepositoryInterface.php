<?php

namespace App\Repositories\Contracts;

use App\Models\Gaji;
use Illuminate\Database\Eloquent\Collection;

interface GajiRepositoryInterface
{
    public function getHistoryByPegawaiId(mixed $pegawaiId): Collection;

    public function findByPeriod(
        mixed $pegawaiId,
        mixed $tahun,
        mixed $bulan
    ): ?Gaji;

    public function loadSlipRelations(Gaji $gaji): Gaji;
}