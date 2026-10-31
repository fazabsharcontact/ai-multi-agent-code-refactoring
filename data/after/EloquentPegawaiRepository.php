<?php

namespace App\Repositories;

use App\Models\Pegawai;
use App\Repositories\Contracts\PegawaiRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class EloquentPegawaiRepository implements PegawaiRepositoryInterface
{
    public function getAllOrderedByName(): Collection
    {
        return Pegawai::orderBy('nama')->get();
    }

    public function getWithoutGajiForPeriod(
        int|string $bulan,
        int|string $tahun,
        Carbon $tanggal
    ): Collection {
        return Pegawai::where('tanggal_masuk', '<=', $tanggal)
            ->whereDoesntHave('gajis', function ($query) use ($bulan, $tahun) {
                $query->where('bulan', $bulan)
                    ->where('tahun', $tahun);
            })
            ->with('jabatan')
            ->orderBy('nama')
            ->get();
    }

    public function findWithRelations(int|string $pegawaiId, array $relations): mixed
    {
        return Pegawai::with($relations)->find($pegawaiId);
    }
}