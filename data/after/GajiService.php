<?php

namespace App\Services;

use App\Models\Gaji;
use App\Repositories\Contracts\GajiReferenceRepositoryInterface;
use App\Repositories\Contracts\GajiRepositoryInterface;
use App\Repositories\Contracts\PegawaiRepositoryInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GajiService
{
    public function __construct(
        private readonly GajiRepositoryInterface $gajiRepository,
        private readonly PegawaiRepositoryInterface $pegawaiRepository,
        private readonly GajiReferenceRepositoryInterface $referenceRepository
    ) {
    }

    public function getIndexData(array $filters): array
    {
        return [
            'gaji' => $this->gajiRepository->paginate($filters, 10),
            'jabatan' => $this->referenceRepository->getAllJabatanOrderedByName(),
            'pegawaiBelumGajian' => $this->getPegawaiBelumGajian(),
        ];
    }

    public function getCreateData(): array
    {
        return [
            'pegawais' => $this->pegawaiRepository->getAllOrderedByName(),
            'masterTunjangans' => $this->referenceRepository->getAllMasterTunjanganOrderedByName(),
            'masterPotongans' => $this->referenceRepository->getAllMasterPotonganOrderedByName(),
        ];
    }

    public function createGaji(
        array $data,
        bool $hasTunjangans,
        bool $hasPotongans
    ): Gaji {
        return $this->gajiRepository->transaction(function () use (
            $data,
            $hasTunjangans,
            $hasPotongans
        ) {
            $totals = $this->calculateTotals($data);

            $gaji = $this->gajiRepository->create(
                $this->buildGajiAttributes($data, $totals)
            );

            $this->storeDetails(
                $gaji,
                $data,
                $hasTunjangans,
                $hasPotongans
            );

            return $gaji;
        });
    }

    public function getEditData(Gaji $gaji): array
    {
        $gaji = $this->gajiRepository->loadRelations(
            $gaji,
            ['pegawai', 'tunjanganDetails', 'potonganDetails']
        );

        return [
            'gaji' => $gaji,
            'pegawais' => $this->pegawaiRepository->getAllOrderedByName(),
            'masterTunjangans' => $this->referenceRepository->getAllMasterTunjanganOrderedByName(),
            'masterPotongans' => $this->referenceRepository->getAllMasterPotonganOrderedByName(),
        ];
    }

    public function updateGaji(
        Gaji $gaji,
        array $data,
        bool $hasTunjangans,
        bool $hasPotongans
    ): void {
        $this->gajiRepository->transaction(function () use (
            $gaji,
            $data,
            $hasTunjangans,
            $hasPotongans
        ) {
            $this->gajiRepository->deleteDetails($gaji);

            $totals = $this->calculateTotals($data);

            $this->gajiRepository->update(
                $gaji,
                $this->buildGajiAttributes($data, $totals)
            );

            $this->storeDetails(
                $gaji,
                $data,
                $hasTunjangans,
                $hasPotongans
            );
        });
    }

    public function deleteGaji(Gaji $gaji): void
    {
        $this->gajiRepository->delete($gaji);
    }

    public function downloadSlipGaji(Gaji $gaji): mixed
    {
        $gaji = $this->gajiRepository->loadRelations(
            $gaji,
            [
                'pegawai.jabatan',
                'tunjanganDetails.masterTunjangan',
                'potonganDetails.masterPotongan',
            ]
        );

        $pdf = Pdf::loadView('admin.gaji.slip-gaji-pdf', ['gaji' => $gaji]);

        $namaFile = 'slip-gaji-'
            . $gaji->pegawai->nama
            . '-'
            . $gaji->bulan
            . '-'
            . $gaji->tahun
            . '.pdf';

        return $pdf->download($namaFile);
    }

    public function cekGajiPegawai(array $data): array
    {
        $gajiExists = $this->gajiRepository->existsForPeriod(
            $data['pegawai_id'],
            $data['bulan'],
            $data['tahun']
        );

        $pegawai = null;

        if ($gajiExists) {
            $pegawai = $this->pegawaiRepository->findWithRelations(
                $data['pegawai_id'],
                ['jabatan', 'tim.divisi']
            );
        }

        return [
            'exists' => $gajiExists,
            'pegawai' => $pegawai,
        ];
    }

    private function getPegawaiBelumGajian(): Collection
    {
        $pegawaiBelumGajian = collect();

        if (Carbon::now()->day > 1) {
            $bulanIni = Carbon::now()->month;
            $tahunIni = Carbon::now()->year;

            $pegawaiBelumGajian = $this->pegawaiRepository
                ->getWithoutGajiForPeriod(
                    $bulanIni,
                    $tahunIni,
                    Carbon::now()
                );
        }

        return $pegawaiBelumGajian;
    }

    private function calculateTotals(array $data): array
    {
        $totalTunjangan = collect($data['tunjangans'] ?? null)->sum('jumlah');
        $totalPotongan = collect($data['potongans'] ?? null)->sum('jumlah');
        $gajiBersih = $data['gaji_pokok'] + $totalTunjangan - $totalPotongan;

        return [
            'total_tunjangan' => $totalTunjangan,
            'total_potongan' => $totalPotongan,
            'gaji_bersih' => $gajiBersih,
        ];
    }

    private function buildGajiAttributes(array $data, array $totals): array
    {
        return [
            'pegawai_id' => $data['pegawai_id'],
            'bulan' => $data['bulan'],
            'tahun' => $data['tahun'],
            'gaji_pokok' => $data['gaji_pokok'],
            'total_tunjangan' => $totals['total_tunjangan'],
            'total_potongan' => $totals['total_potongan'],
            'gaji_bersih' => $totals['gaji_bersih'],
        ];
    }

    private function storeDetails(
        Gaji $gaji,
        array $data,
        bool $hasTunjangans,
        bool $hasPotongans
    ): void {
        if ($hasTunjangans) {
            $this->gajiRepository->createTunjanganDetails(
                $gaji,
                $data['tunjangans']
            );
        }

        if ($hasPotongans) {
            $this->gajiRepository->createPotonganDetails(
                $gaji,
                $data['potongans']
            );
        }
    }
}