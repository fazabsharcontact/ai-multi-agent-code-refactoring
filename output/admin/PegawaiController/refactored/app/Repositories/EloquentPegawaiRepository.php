<?php

namespace App\Repositories;

use App\Models\Pegawai;
use App\Models\User;
use App\Repositories\Contracts\PegawaiRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentPegawaiRepository implements PegawaiRepositoryInterface
{
    public function paginate(
        ?string $jabatanFilter,
        ?string $search,
        int $perPage = 10
    ): LengthAwarePaginator {
        return Pegawai::with('jabatan', 'user', 'tim.divisi')
            ->when(
                $jabatanFilter,
                fn ($query) => $query->whereHas(
                    'jabatan',
                    fn ($jabatanQuery) => $jabatanQuery->where(
                        'nama_jabatan',
                        $jabatanFilter
                    )
                )
            )
            ->when(
                $search,
                fn ($query) => $query->where(
                    'nama',
                    'like',
                    "%{$search}%"
                )
            )
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function make(): Pegawai
    {
        return new Pegawai();
    }

    public function loadUser(Pegawai $pegawai): Pegawai
    {
        return $pegawai->load('user');
    }

    public function createForUser(User $user, array $data): Pegawai
    {
        return $user->pegawai()->create($data);
    }

    public function update(Pegawai $pegawai, array $data): bool
    {
        return $pegawai->update($data);
    }

    public function deleteRelatedUser(Pegawai $pegawai): int
    {
        return $pegawai->user()->delete();
    }
}