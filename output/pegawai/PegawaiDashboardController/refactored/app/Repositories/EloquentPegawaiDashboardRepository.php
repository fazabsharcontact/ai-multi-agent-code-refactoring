<?php

namespace App\Repositories;

use App\Models\Gaji;
use App\Models\Kehadiran;
use App\Models\Pegawai;
use App\Models\PengumumanPenerima;
use App\Repositories\Contracts\PegawaiDashboardRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentPegawaiDashboardRepository implements PegawaiDashboardRepositoryInterface
{
    public function findPegawaiByUserId(mixed $userId): ?Pegawai
    {
        return Pegawai::where('user_id', $userId)->first();
    }

    public function countKehadiranByStatus(
        mixed $pegawaiId,
        string $status,
        int $bulan,
        int $tahun
    ): int {
        return Kehadiran::where('pegawai_id', $pegawaiId)
            ->where('status', $status)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->count();
    }

    public function getTotalGajiTahunan(mixed $pegawaiId, int $tahun): mixed
    {
        return Gaji::where('pegawai_id', $pegawaiId)
            ->where('tahun', $tahun)
            ->sum('gaji_bersih');
    }

    public function getGajiPerBulan(mixed $pegawaiId, int $tahun): array
    {
        return Gaji::where('pegawai_id', $pegawaiId)
            ->where('tahun', $tahun)
            ->orderBy('bulan')
            ->pluck('gaji_bersih', 'bulan')
            ->toArray();
    }

    public function getPengumumanForPegawai(Pegawai $pegawai): Collection
    {
        return PengumumanPenerima::with('pengumuman')
            ->where(function ($query) use ($pegawai) {
                $query->where(function ($pegawaiQuery) use ($pegawai) {
                    $pegawaiQuery->where('target_type', 'pegawai')
                        ->where('target_id', $pegawai->id);
                })
                    ->orWhere(function ($jabatanQuery) use ($pegawai) {
                        $jabatanQuery->where('target_type', 'jabatan')
                            ->where('target_id', $pegawai->jabatan_id);
                    })
                    ->orWhere(function ($divisiQuery) use ($pegawai) {
                        $divisiQuery->where('target_type', 'divisi')
                            ->where('target_id', $pegawai->divisi_id);
                    })
                    ->orWhere(function ($timQuery) use ($pegawai) {
                        $timQuery->where('target_type', 'tim')
                            ->where('target_id', $pegawai->tim_id);
                    })
                    ->orWhere('target_type', 'semua');
            })
            ->orderByDesc('created_at')
            ->get();
    }
}