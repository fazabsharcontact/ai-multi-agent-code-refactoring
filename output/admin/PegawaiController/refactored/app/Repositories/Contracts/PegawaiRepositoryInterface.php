<?php

namespace App\Repositories\Contracts;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PegawaiRepositoryInterface
{
    public function paginate(
        ?string $jabatanFilter,
        ?string $search,
        int $perPage = 10
    ): LengthAwarePaginator;

    public function make(): Pegawai;

    public function loadUser(Pegawai $pegawai): Pegawai;

    public function createForUser(User $user, array $data): Pegawai;

    public function update(Pegawai $pegawai, array $data): bool;

    public function deleteRelatedUser(Pegawai $pegawai): int;
}