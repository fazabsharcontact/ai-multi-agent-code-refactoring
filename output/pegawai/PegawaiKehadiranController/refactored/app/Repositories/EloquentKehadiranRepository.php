<?php

namespace App\Repositories;

use App\Models\Kehadiran;
use App\Repositories\Contracts\KehadiranRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentKehadiranRepository implements KehadiranRepositoryInterface
{
    public function paginateByPegawaiAndPeriod(
        int $pegawaiId,
        mixed $tahun,
        mixed $bulan,
        int $perPage = 10
    ): LengthAwarePaginator {
        return Kehadiran::where('pegawai_id', $pegawaiId)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->orderByDesc('tanggal')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findByPegawaiAndDate(
        int $pegawaiId,
        string $tanggal
    ): ?Kehadiran {
        return Kehadiran::where('pegawai_id', $pegawaiId)
            ->whereDate('tanggal', $tanggal)
            ->first();
    }

    public function create(array $data): Kehadiran
    {
        return Kehadiran::create($data);
    }

    public function updateJamPulang(
        Kehadiran $kehadiran,
        string $jamPulang
    ): bool {
        return $kehadiran->update([
            'jam_pulang' => $jamPulang,
        ]);
    }
}