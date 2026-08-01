<?php

namespace App\Repositories\Contracts;

use DateTimeInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface DashboardRepositoryInterface
{
    public function countPegawai(): int;

    public function getPeriodeGajiTerbaru(): ?object;

    public function getTotalGajiByPeriode(mixed $tahun, mixed $bulan): mixed;

    public function getPegawaiPerJabatan(): array;

    public function getAktivitasTerbaru(
        DateTimeInterface $jangkauanWaktu,
        int $limit = 5
    ): Collection;

    public function getPengumumans(int $perPage = 10): LengthAwarePaginator;

    public function getMeetings(int $perPage = 10): LengthAwarePaginator;
}