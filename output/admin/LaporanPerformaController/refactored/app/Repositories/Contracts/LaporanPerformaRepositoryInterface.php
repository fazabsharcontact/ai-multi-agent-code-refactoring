<?php

namespace App\Repositories\Contracts;

use App\Models\Divisi;
use App\Models\Pegawai;
use App\Models\Tim;
use App\Repositories\EloquentLaporanPerformaRepository;
use Carbon\Carbon;
use Illuminate\Container\Attributes\Bind;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

#[Bind(EloquentLaporanPerformaRepository::class)]
interface LaporanPerformaRepositoryInterface
{
    public function getFilterOptions(): array;

    public function findDivisi(mixed $id): ?Divisi;

    public function findTim(mixed $id): ?Tim;

    public function findPegawai(mixed $id): ?Pegawai;

    public function getPegawaiPerformance(
        Carbon $tanggalMulai,
        Carbon $tanggalSelesai,
        ?string $filterType = null,
        mixed $filterId = null
    ): Collection;

    public function getKehadiranDetails(
        Collection $pegawaiIds,
        Carbon $tanggalMulai,
        Carbon $tanggalSelesai,
        bool $paginate = true
    ): LengthAwarePaginator|Collection;
}