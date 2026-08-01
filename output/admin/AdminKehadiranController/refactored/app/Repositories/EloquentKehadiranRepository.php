<?php

namespace App\Repositories;

use App\Models\Kehadiran;
use App\Models\Pegawai;
use App\Repositories\Contracts\KehadiranRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentKehadiranRepository implements KehadiranRepositoryInterface
{
    public function __construct(
        private readonly Kehadiran $kehadiran,
        private readonly Pegawai $pegawai
    ) {
    }

    public function paginateByPeriod($tahun, $bulan, $pegawaiId = null): LengthAwarePaginator
    {
        return $this->kehadiran
            ->newQuery()
            ->with('pegawai.user')
            ->when($pegawaiId, function ($query) use ($pegawaiId) {
                $query->where('pegawai_id', $pegawaiId);
            })
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();
    }

    public function getRekapByPeriod($tahun, $bulan, $pegawaiId = null): Collection
    {
        return $this->kehadiran
            ->newQuery()
            ->selectRaw('pegawai_id,
                SUM(CASE WHEN status="Hadir" THEN 1 ELSE 0 END) as total_hadir,
                SUM(CASE WHEN status="Absen" THEN 1 ELSE 0 END) as total_absen,
                SUM(CASE WHEN status="Sakit" THEN 1 ELSE 0 END) as total_sakit,
                SUM(CASE WHEN status="Izin" THEN 1 ELSE 0 END) as total_izin,
                SUM(CASE WHEN status="Terlambat" THEN 1 ELSE 0 END) as total_terlambat,
                SUM(CASE WHEN status="Cuti" THEN 1 ELSE 0 END) as total_cuti')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->when($pegawaiId, fn ($query) => $query->where('pegawai_id', $pegawaiId))
            ->groupBy('pegawai_id')
            ->with('pegawai.user')
            ->get();
    }

    public function getAllPegawaiForSelection(): Collection
    {
        return $this->pegawai
            ->newQuery()
            ->select('id', 'nama')
            ->orderBy('nama')
            ->get();
    }

    public function findPegawaiWithUserOrFail($pegawaiId): Pegawai
    {
        return $this->pegawai
            ->newQuery()
            ->with('user')
            ->findOrFail($pegawaiId);
    }

    public function getByPegawaiAndPeriod($pegawaiId, $tahun, $bulan): Collection
    {
        return $this->kehadiran
            ->newQuery()
            ->where('pegawai_id', $pegawaiId)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findKehadiranOrFail($id): Kehadiran
    {
        return $this->kehadiran
            ->newQuery()
            ->findOrFail($id);
    }
}