<?php

namespace App\Repositories;

use App\Models\Cuti;
use App\Repositories\Contracts\CutiRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentCutiRepository implements CutiRepositoryInterface
{
    public function paginateWithPegawai(
        mixed $statusFilter,
        mixed $search,
        int $perPage = 10,
        string $pageName = 'cuti_page'
    ): LengthAwarePaginator {
        $query = Cuti::with('pegawai.jabatan', 'pegawai.sisaCuti')->latest();

        $query->when(
            $statusFilter,
            fn ($builder) => $builder->where('status', $statusFilter)
        );

        $query->when(
            $search,
            fn ($builder) => $builder->whereHas(
                'pegawai',
                fn ($pegawaiQuery) => $pegawaiQuery->where('nama', 'like', "%{$search}%")
            )
        );

        return $query->paginate($perPage, ['*'], $pageName);
    }

    public function updateStatus(Cuti $cuti, mixed $status, mixed $approvedById): bool
    {
        $cuti->status = $status;
        $cuti->disetujui_oleh_id = $approvedById;

        return $cuti->save();
    }
}