<?php

namespace App\Repositories;

use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\Tim;
use App\Repositories\Contracts\PengumumanTargetRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentPengumumanTargetRepository implements PengumumanTargetRepositoryInterface
{
    public function getPegawais(): Collection
    {
        return Pegawai::orderBy('nama')->get();
    }

    public function getJabatans(): Collection
    {
        return Jabatan::orderBy('nama_jabatan')->get();
    }

    public function getTims(): Collection
    {
        return Tim::orderBy('nama_tim')->get();
    }

    public function getDivisis(): Collection
    {
        return Divisi::orderBy('nama_divisi')->get();
    }
}