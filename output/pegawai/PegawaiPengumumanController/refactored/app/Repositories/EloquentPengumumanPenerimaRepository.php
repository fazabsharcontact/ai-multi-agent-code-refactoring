<?php

namespace App\Repositories;

use App\Models\Pegawai;
use App\Models\PengumumanPenerima;
use App\Repositories\Contracts\PengumumanPenerimaRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class EloquentPengumumanPenerimaRepository implements PengumumanPenerimaRepositoryInterface
{
    /**
     * @return Collection<int, PengumumanPenerima>
     */
    public function getForPegawai(Pegawai $pegawai): Collection
    {
        return PengumumanPenerima::with('pengumuman')
            ->where(function (Builder $query) use ($pegawai): void {
                $query->where(function (Builder $targetQuery) use ($pegawai): void {
                    $targetQuery->where('target_type', 'pegawai')
                        ->where('target_id', $pegawai->id);
                })
                    ->orWhere(function (Builder $targetQuery) use ($pegawai): void {
                        $targetQuery->where('target_type', 'jabatan')
                            ->where('target_id', $pegawai->jabatan_id);
                    })
                    ->orWhere(function (Builder $targetQuery) use ($pegawai): void {
                        $targetQuery->where('target_type', 'divisi')
                            ->where('target_id', $pegawai->divisi_id);
                    })
                    ->orWhere(function (Builder $targetQuery) use ($pegawai): void {
                        $targetQuery->where('target_type', 'tim')
                            ->where('target_id', $pegawai->tim_id);
                    })
                    ->orWhere('target_type', 'semua');
            })
            ->orderByDesc('created_at')
            ->get();
    }
}