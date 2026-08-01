<?php

namespace App\Services;

use App\Models\PengumumanPenerima;
use App\Repositories\Contracts\PengumumanPenerimaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class PegawaiPengumumanService
{
    public function __construct(
        private readonly PengumumanPenerimaRepositoryInterface $pengumumanPenerimaRepository
    ) {
    }

    /**
     * @return Collection<int, PengumumanPenerima>
     */
    public function getPengumumansForAuthenticatedPegawai(): Collection
    {
        $pegawai = Auth::user()->pegawai;

        return $this->pengumumanPenerimaRepository->getForPegawai($pegawai);
    }
}