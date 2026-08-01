<?php

namespace App\Repositories;

use App\Models\Pegawai;
use App\Repositories\Contracts\PegawaiRepositoryInterface;

class EloquentPegawaiRepository implements PegawaiRepositoryInterface
{
    public function loadSisaCuti(Pegawai $pegawai): Pegawai
    {
        return $pegawai->load('sisaCuti');
    }

    public function createDefaultSisaCuti(Pegawai $pegawai, int $sisaCuti = 12): void
    {
        $pegawai->sisaCuti()->create([
            'sisa_cuti' => $sisaCuti,
        ]);
    }
}