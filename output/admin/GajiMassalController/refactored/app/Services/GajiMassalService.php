<?php

namespace App\Services;

use App\Data\GajiMassalFilterData;
use App\Repositories\Contracts\GajiMassalRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GajiMassalService
{
    public function __construct(
        private readonly GajiMassalRepositoryInterface $repository,
    ) {
    }

    public function getLangkahSatuData(
        GajiMassalFilterData $filterData,
    ): array {
        $pegawais = collect();

        if ($filterData->shouldFilter) {
            $pegawais = $this->repository->getFilteredPegawais(
                $filterData->filters,
            );
        }

        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        return [
            'divisis' => $this->repository->getDivisis(),
            'tims' => $this->repository->getTims(),
            'jabatans' => $this->repository->getJabatans(),
            'pegawais' => $pegawais,
            'inputs' => $filterData->inputs,
            'pegawaiBelumGajian' => $this->repository
                ->getPegawaiBelumGajian(
                    $bulanIni,
                    $tahunIni,
                    Carbon::now(),
                ),
        ];
    }

    public function getLangkahDuaData(
        array $pegawaiIds,
        mixed $bulan,
        mixed $tahun,
    ): array {
        return [
            'pegawais' => $this->repository->getPegawaisByIds($pegawaiIds),
            'bulan' => $bulan,
            'tahun' => $tahun,
            'masterTunjangans' => $this->repository->getMasterTunjangans(),
            'masterPotongans' => $this->repository->getMasterPotongans(),
        ];
    }

    public function simpanGajiMassal(
        array $validated,
        array $tunjangansUmum,
        array $potongansUmum,
    ): int {
        $totalTunjanganUmum = collect($tunjangansUmum)->sum('jumlah');
        $totalPotonganUmum = collect($potongansUmum)->sum('jumlah');

        $this->repository->transaction(function () use (
            $validated,
            $tunjangansUmum,
            $potongansUmum,
            $totalTunjanganUmum,
            $totalPotonganUmum,
        ): void {
            foreach ($validated['pegawai_gaji'] as $dataPegawai) {
                $this->simpanGajiPegawai(
                    dataPegawai: $dataPegawai,
                    bulan: $validated['bulan'],
                    tahun: $validated['tahun'],
                    tunjangansUmum: $tunjangansUmum,
                    potongansUmum: $potongansUmum,
                    totalTunjanganUmum: $totalTunjanganUmum,
                    totalPotonganUmum: $totalPotonganUmum,
                );
            }
        });

        return count($validated['pegawai_gaji']);
    }

    public function getPegawaiSudahGajian(
        array $pegawaiIds,
        int $bulan,
        int $tahun,
    ): Collection {
        return $this->repository->getPegawaiSudahGajian(
            $pegawaiIds,
            $bulan,
            $tahun,
        );
    }

    private function simpanGajiPegawai(
        array $dataPegawai,
        int $bulan,
        int $tahun,
        array $tunjangansUmum,
        array $potongansUmum,
        mixed $totalTunjanganUmum,
        mixed $totalPotonganUmum,
    ): void {
        $gajiPokok = $dataPegawai['gaji_pokok'];
        $gajiBersih = $gajiPokok
            + $totalTunjanganUmum
            - $totalPotonganUmum;

        $gaji = $this->repository->createGaji([
            'pegawai_id' => $dataPegawai['pegawai_id'],
            'bulan' => $bulan,
            'tahun' => $tahun,
            'gaji_pokok' => $gajiPokok,
            'total_tunjangan' => $totalTunjanganUmum,
            'total_potongan' => $totalPotonganUmum,
            'gaji_bersih' => $gajiBersih,
        ]);

        foreach ($tunjangansUmum as $tunjangan) {
            $this->repository->createTunjanganDetail($gaji, $tunjangan);
        }

        foreach ($potongansUmum as $potongan) {
            $this->repository->createPotonganDetail($gaji, $potongan);
        }
    }
}