<?php

namespace App\Repositories\Contracts;

use App\Models\Pegawai;
use App\Models\PengumumanPenerima;
use Illuminate\Database\Eloquent\Collection;

interface PengumumanPenerimaRepositoryInterface
{
    /**
     * @return Collection<int, PengumumanPenerima>
     */
    public function getForPegawai(Pegawai $pegawai): Collection;
}