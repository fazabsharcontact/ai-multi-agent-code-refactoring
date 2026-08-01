<?php

namespace App\Repositories;

use App\Models\Jabatan;
use App\Repositories\Contracts\JabatanRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentJabatanRepository implements JabatanRepositoryInterface
{
    public function getFiltered(mixed $search = null, mixed $jabatanFilter = null): Collection
    {
        $query = Jabatan::query();

        $query->when($search, function ($query) use ($search): void {
            $query->where('nama_jabatan', 'like', "%{$search}%");
        });

        $query->when($jabatanFilter, function ($query) use ($jabatanFilter): void {
            $query->where('nama_jabatan', $jabatanFilter);
        });

        return $query->latest()->get();
    }

    public function create(array $data): Jabatan
    {
        return Jabatan::create($data);
    }

    public function update(Jabatan $jabatan, array $data): void
    {
        $jabatan->update($data);
    }

    public function delete(Jabatan $jabatan): void
    {
        $jabatan->delete();
    }
}