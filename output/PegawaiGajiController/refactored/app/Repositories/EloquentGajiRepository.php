<?php

namespace App\Repositories;

use App\Models\Gaji;
use App\Repositories\Contracts\GajiRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentGajiRepository implements GajiRepositoryInterface
{
    public function getHistoryByPegawaiId(mixed $pegawaiId): Collection
    {
        return Gaji::where('pegawai_id', $pegawaiId)
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->get();
    }

    public function findByPeriod(
        mixed $pegawaiId,
        mixed $tahun,
        mixed $bulan
    ): ?Gaji {
        return Gaji::where('pegawai_id', $pegawaiId)
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->with([
                'tunjanganDetails.masterTunjangan',
                'potonganDetails.masterPotongan',
                'pegawai.jabatan',
            ])
            ->first();
    }

    public function loadSlipRelations(Gaji $gaji): Gaji
    {
        return $gaji->load([
            'pegawai.jabatan',
            'tunjanganDetails.masterTunjangan',
            'potonganDetails.masterPotongan',
        ]);
    }
}