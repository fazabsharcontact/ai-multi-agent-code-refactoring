<?php

namespace App\Repositories;

use App\Models\Pengumuman;
use App\Repositories\Contracts\PengumumanRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentPengumumanRepository implements PengumumanRepositoryInterface
{
    public function paginateLatestWithPembuat(int $perPage): LengthAwarePaginator
    {
        return Pengumuman::with('pembuat')
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $attributes): Pengumuman
    {
        return Pengumuman::create($attributes);
    }

    public function createPenerima(Pengumuman $pengumuman, array $attributes): void
    {
        $pengumuman->penerimas()->create($attributes);
    }

    public function delete(Pengumuman $pengumuman): ?bool
    {
        return $pengumuman->delete();
    }
}