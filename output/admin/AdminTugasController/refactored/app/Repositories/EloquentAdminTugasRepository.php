<?php

namespace App\Repositories;

use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\Tim;
use App\Models\Tugas;
use App\Repositories\Contracts\AdminTugasRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class EloquentAdminTugasRepository implements AdminTugasRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Tugas::with([
            'pemberi',
            'penerima.jabatan',
            'penerima.tim.divisi',
        ])->latest();

        $this->applySearch($query, $filters);
        $this->applyFilters($query, $filters);

        return $query->paginate($perPage)->withQueryString();
    }

    public function getPegawais(): Collection
    {
        return Pegawai::orderBy('nama')->get();
    }

    public function getJabatans(): Collection
    {
        return Jabatan::orderBy('nama_jabatan')->get();
    }

    public function getTims(): Collection
    {
        return Tim::orderBy('nama_tim')->get();
    }

    public function getDivisis(): Collection
    {
        return Divisi::orderBy('nama_divisi')->get();
    }

    public function create(array $data): Tugas
    {
        return Tugas::create($data);
    }

    private function applySearch(Builder $query, array $filters): void
    {
        if (!array_key_exists('search', $filters)) {
            return;
        }

        $search = $filters['search'];

        $query->where(function (Builder $subquery) use ($search): void {
            $subquery
                ->where('judul_tugas', 'like', "%{$search}%")
                ->orWhereHas(
                    'penerima',
                    fn (Builder $query) => $query->where('nama', 'like', "%{$search}%")
                );
        });
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        if (array_key_exists('bulan', $filters)) {
            $query->whereMonth('created_at', $filters['bulan']);
        }

        if (array_key_exists('tahun', $filters)) {
            $query->whereYear('created_at', $filters['tahun']);
        }

        if (array_key_exists('jabatan_id', $filters)) {
            $query->whereHas(
                'penerima.jabatan',
                fn (Builder $query) => $query->where('id', 'like', $filters['jabatan_id'])
            );
        }

        if (array_key_exists('tim_id', $filters)) {
            $query->whereHas(
                'penerima',
                fn (Builder $query) => $query->where('tim_id', $filters['tim_id'])
            );
        }

        if (array_key_exists('divisi_id', $filters)) {
            $query->whereHas(
                'penerima.tim.divisi',
                fn (Builder $query) => $query->where('id', $filters['divisi_id'])
            );
        }

        if (array_key_exists('status', $filters)) {
            $query->where('status', $filters['status']);
        }
    }
}