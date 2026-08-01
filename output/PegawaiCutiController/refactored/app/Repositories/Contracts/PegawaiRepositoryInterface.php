<?php

namespace App\Repositories\Contracts;

use App\Models\Pegawai;

interface PegawaiRepositoryInterface
{
    public function loadSisaCuti(Pegawai $pegawai): Pegawai;

    public function createDefaultSisaCuti(Pegawai $pegawai, int $sisaCuti = 12): void;
}