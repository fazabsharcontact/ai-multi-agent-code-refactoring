<?php

namespace App\Repositories\Contracts;

use App\Models\Jabatan;
use Illuminate\Database\Eloquent\Collection;

interface JabatanRepositoryInterface
{
    public function getFiltered(mixed $search = null, mixed $jabatanFilter = null): Collection;

    public function create(array $data): Jabatan;

    public function update(Jabatan $jabatan, array $data): void;

    public function delete(Jabatan $jabatan): void;
}