<?php

namespace App\Repositories\Contracts;

use App\Models\Pengumuman;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PengumumanRepositoryInterface
{
    public function paginateLatestWithPembuat(int $perPage): LengthAwarePaginator;

    public function create(array $attributes): Pengumuman;

    public function createPenerima(Pengumuman $pengumuman, array $attributes): void;

    public function delete(Pengumuman $pengumuman): ?bool;
}