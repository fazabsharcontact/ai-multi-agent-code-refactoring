<?php

namespace App\Repositories\Contracts;

use App\Models\Gaji;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Collection;

interface GajiMassalRepositoryInterface
{
    public function getFilteredPegawais(array $filters): Collection;

    public function getPegawaiBelumGajian(
        int $bulan,
        int $tahun,
        Carbon $tanggalSekarang,
    ): Collection;

    public function getDivisis(): Collection;

    public function getTims(): Collection;

    public function getJabatans(): Collection;

    public function getPegawaisByIds(array $pegawaiIds): Collection;

    public function getMasterTunjangans(): Collection;

    public function getMasterPotongans(): Collection;

    public function getPegawaiSudahGajian(
        array $pegawaiIds,
        int $bulan,
        int $tahun,
    ): Collection;

    public function createGaji(array $data): Gaji;

    public function createTunjanganDetail(Gaji $gaji, array $data): void;

    public function createPotonganDetail(Gaji $gaji, array $data): void;

    public function transaction(Closure $callback): mixed;
}