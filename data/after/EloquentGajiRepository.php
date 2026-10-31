<?php

namespace App\Repositories;

use App\Models\Gaji;
use App\Repositories\Contracts\GajiRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class EloquentGajiRepository implements GajiRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;
        $jabatan = $filters['jabatan'] ?? null;
        $bulan = $filters['bulan'] ?? null;
        $tahun = $filters['tahun'] ?? null;

        $query = Gaji::with(['pegawai.jabatan'])->latest();

        $query->when(
            $search,
            fn ($query) => $query->whereHas(
                'pegawai',
                fn ($pegawaiQuery) => $pegawaiQuery->where('nama', 'like', "%{$search}%")
            )
        );

        $query->when(
            $jabatan,
            fn ($query) => $query->whereHas(
                'pegawai.jabatan',
                fn ($jabatanQuery) => $jabatanQuery->where('nama_jabatan', $jabatan)
            )
        );

        $query->when(
            $bulan,
            fn ($query) => $query->where('bulan', $bulan)
        );

        $query->when(
            $tahun,
            fn ($query) => $query->where('tahun', $tahun)
        );

        return $query->paginate($perPage);
    }

    public function create(array $attributes): Gaji
    {
        return Gaji::create($attributes);
    }

    public function update(Gaji $gaji, array $attributes): bool
    {
        return $gaji->update($attributes);
    }

    public function delete(Gaji $gaji): bool
    {
        return (bool) $gaji->delete();
    }

    public function deleteDetails(Gaji $gaji): void
    {
        $gaji->tunjanganDetails()->delete();
        $gaji->potonganDetails()->delete();
    }

    public function createTunjanganDetails(Gaji $gaji, iterable $tunjangans): void
    {
        foreach ($tunjangans as $tunjangan) {
            $gaji->tunjanganDetails()->create($tunjangan);
        }
    }

    public function createPotonganDetails(Gaji $gaji, iterable $potongans): void
    {
        foreach ($potongans as $potongan) {
            $gaji->potonganDetails()->create($potongan);
        }
    }

    public function loadRelations(Gaji $gaji, array $relations): Gaji
    {
        return $gaji->load($relations);
    }

    public function existsForPeriod(
        int|string $pegawaiId,
        int|string $bulan,
        int|string $tahun
    ): bool {
        return Gaji::where('pegawai_id', $pegawaiId)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->exists();
    }

    public function transaction(callable $callback): mixed
    {
        return DB::transaction($callback);
    }
}