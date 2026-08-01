<?php

namespace App\Repositories\Contracts;

use App\Models\Kehadiran;
use App\Models\Pegawai;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface KehadiranRepositoryInterface
{
    public function paginateByPeriod($tahun, $bulan, $pegawaiId = null): LengthAwarePaginator;

    public function getRekapByPeriod($tahun, $bulan, $pegawaiId = null): Collection;

    public function getAllPegawaiForSelection(): Collection;

    public function findPegawaiWithUserOrFail($pegawaiId): Pegawai;

    public function getByPegawaiAndPeriod($pegawaiId, $tahun, $bulan): Collection;

    public function findKehadiranOrFail($id): Kehadiran;
}