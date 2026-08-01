<?php

namespace App\Repositories;

use App\Models\Pegawai;
use App\Repositories\Contracts\PegawaiRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentPegawaiRepository implements PegawaiRepositoryInterface
{
    public function paginateWithCutiSummary(
        mixed $search,
        int $perPage = 10,
        string $pageName = 'pegawai_page'
    ): LengthAwarePaginator {
        return Pegawai::with(['jabatan', 'sisaCuti'])
            ->when(
                $search,
                fn ($builder) => $builder->where('nama', 'like', "%{$search}%")
            )
            ->orderBy('nama')
            ->paginate($perPage, ['*'], $pageName);
    }
}