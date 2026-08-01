<?php

namespace App\Repositories;

use App\Models\Pegawai;
use App\Repositories\Contracts\PegawaiRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentPegawaiRepository implements PegawaiRepositoryInterface
{
    public function getAllOrderedByName(): Collection
    {
        return Pegawai::orderBy('nama')->get();
    }
}