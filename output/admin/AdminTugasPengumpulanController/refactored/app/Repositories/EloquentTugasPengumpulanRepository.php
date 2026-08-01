<?php

namespace App\Repositories;

use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\Tim;
use App\Models\Tugas;
use App\Models\TugasPengumpulan;
use App\Repositories\Contracts\TugasPengumpulanRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentTugasPengumpulanRepository implements TugasPengumpulanRepositoryInterface
{
    public function paginateWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = TugasPengumpulan::with([
            'tugas',
            'pegawai.user',
            'pegawai.jabatan',
            'pegawai.tim.divisi',
        ])->orderBy('created_at', 'desc');

        $query->when(filled($filters['search'] ?? null), function ($query) use ($filters) {
            $search = $filters['search'];

            $query
                ->whereHas(
                    'tugas',
                    fn ($subQuery) => $subQuery->where('judul_tugas', 'like', "%{$search}%")
                )
                ->orWhereHas(
                    'pegawai',
                    fn ($subQuery) => $subQuery->where('nama', 'like', "%{$search}%")
                );
        });

        $query->when(
            filled($filters['bulan'] ?? null),
            fn ($query) => $query->whereMonth('created_at', $filters['bulan'])
        );

        $query->when(
            filled($filters['tahun'] ?? null),
            fn ($query) => $query->whereYear('created_at', $filters['tahun'])
        );

        $query->when(
            filled($filters['status'] ?? null),
            fn ($query) => $query->where('status', $filters['status'])
        );

        $query->when(
            filled($filters['jabatan_id'] ?? null),
            fn ($query) => $query->whereHas(
                'pegawai.jabatan',
                fn ($subQuery) => $subQuery->where('id', $filters['jabatan_id'])
            )
        );

        $query->when(
            filled($filters['tim_id'] ?? null),
            fn ($query) => $query->whereHas(
                'pegawai.tim',
                fn ($subQuery) => $subQuery->where('id', $filters['tim_id'])
            )
        );

        $query->when(
            filled($filters['divisi_id'] ?? null),
            fn ($query) => $query->whereHas(
                'pegawai.tim.divisi',
                fn ($subQuery) => $subQuery->where('id', $filters['divisi_id'])
            )
        );

        return $query->paginate($perPage)->withQueryString();
    }

    public function getAllJabatansOrderedByName(): Collection
    {
        return Jabatan::orderBy('nama_jabatan')->get();
    }

    public function getAllTimsOrderedByName(): Collection
    {
        return Tim::orderBy('nama_tim')->get();
    }

    public function getAllDivisisOrderedByName(): Collection
    {
        return Divisi::orderBy('nama_divisi')->get();
    }

    public function findOrFail(mixed $id): TugasPengumpulan
    {
        return TugasPengumpulan::findOrFail($id);
    }

    public function saveTugasPengumpulan(TugasPengumpulan $tugasPengumpulan): bool
    {
        return $tugasPengumpulan->save();
    }

    public function saveTugas(Tugas $tugas): bool
    {
        return $tugas->save();
    }
}