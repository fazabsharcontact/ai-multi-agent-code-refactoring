<?php

namespace App\Repositories;

use App\Models\Jabatan;
use App\Models\MasterPotongan;
use App\Models\MasterTunjangan;
use App\Repositories\Contracts\GajiReferenceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentGajiReferenceRepository implements GajiReferenceRepositoryInterface
{
    public function getAllJabatanOrderedByName(): Collection
    {
        return Jabatan::orderBy('nama_jabatan')->get();
    }

    public function getAllMasterTunjanganOrderedByName(): Collection
    {
        return MasterTunjangan::orderBy('nama_tunjangan')->get();
    }

    public function getAllMasterPotonganOrderedByName(): Collection
    {
        return MasterPotongan::orderBy('nama_potongan')->get();
    }
}