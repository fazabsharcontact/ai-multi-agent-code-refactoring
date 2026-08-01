<?php

namespace App\Repositories;

use App\Models\Divisi;
use App\Models\Kehadiran;
use App\Models\Pegawai;
use App\Models\Tim;
use App\Repositories\Contracts\LaporanPerformaRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EloquentLaporanPerformaRepository implements LaporanPerformaRepositoryInterface
{
    public function getFilterOptions(): array
    {
        return [
            'divisis' => Divisi::orderBy('nama_divisi')->get(),
            'tims' => Tim::orderBy('nama_tim')->get(),
            'pegawais' => Pegawai::orderBy('nama')->get(),
        ];
    }

    public function findDivisi(mixed $id): ?Divisi
    {
        return Divisi::find($id);
    }

    public function findTim(mixed $id): ?Tim
    {
        return Tim::find($id);
    }

    public function findPegawai(mixed $id): ?Pegawai
    {
        return Pegawai::find($id);
    }

    public function getPegawaiPerformance(
        Carbon $tanggalMulai,
        Carbon $tanggalSelesai,
        ?string $filterType = null,
        mixed $filterId = null
    ): Collection {
        $query = Pegawai::query();

        $this->applyPegawaiFilter($query, $filterType, $filterId);

        return $query
            ->with([
                'kehadirans' => fn ($query) => $query
                    ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
                    ->whereRaw('DAYOFWEEK(tanggal) BETWEEN 2 AND 6'),
                'tugasDiterima' => fn ($query) => $query
                    ->whereBetween('created_at', [$tanggalMulai, $tanggalSelesai])
                    ->whereRaw('DAYOFWEEK(created_at) BETWEEN 2 AND 6'),
            ])
            ->orderBy('nama')
            ->get();
    }

    public function getKehadiranDetails(
        Collection $pegawaiIds,
        Carbon $tanggalMulai,
        Carbon $tanggalSelesai,
        bool $paginate = true
    ): LengthAwarePaginator|Collection {
        $query = Kehadiran::with('pegawai')
            ->whereIn('pegawai_id', $pegawaiIds)
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->whereRaw('DAYOFWEEK(tanggal) BETWEEN 2 AND 6')
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc');

        if ($paginate) {
            return $query->paginate(15, ['*'], 'kehadiran_page');
        }

        return $query->get();
    }

    private function applyPegawaiFilter(
        Builder $query,
        ?string $filterType,
        mixed $filterId
    ): void {
        if ($filterType === 'divisi') {
            $query->whereHas(
                'tim.divisi',
                fn ($query) => $query->where('id', $filterId)
            );

            return;
        }

        if ($filterType === 'tim') {
            $query->where('tim_id', $filterId);

            return;
        }

        if ($filterType === 'pegawai') {
            $query->where('id', $filterId);
        }
    }
}