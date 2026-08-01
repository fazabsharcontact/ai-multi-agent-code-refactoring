<?php

namespace App\Repositories;

use App\Models\Jabatan;
use App\Repositories\Contracts\JabatanRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentJabatanRepository implements JabatanRepositoryInterface
{
    public function getAllOrderedByName(): Collection
    {
        return Jabatan::orderBy('nama_jabatan')->get();
    }
}