<?php

namespace App\Services;

use App\Models\Gaji;
use App\Repositories\Contracts\GajiRepositoryInterface;
use App\Repositories\Contracts\PegawaiRepositoryInterface;
use Barryvdh\DomPDF\Facade\Pdf;

class PegawaiGajiService
{
    public function __construct(
        private readonly PegawaiRepositoryInterface $pegawaiRepository,
        private readonly GajiRepositoryInterface $gajiRepository
    ) {
    }

    public function getIndexData(
        mixed $userId,
        mixed $tahun,
        mixed $bulan
    ): ?array {
        $pegawai = $this->pegawaiRepository->findByUserId($userId);

        if (!$pegawai) {
            return null;
        }

        $riwayat = $this->gajiRepository->getHistoryByPegawaiId($pegawai->id);
        $detail = $this->gajiRepository->findByPeriod(
            $pegawai->id,
            $tahun,
            $bulan
        );

        return [
            'riwayat' => $riwayat,
            'detail' => $detail,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'bulanNama' => $this->getMonthNames(),
        ];
    }

    public function generateSalarySlip(mixed $userId, Gaji $gaji): mixed
    {
        $pegawai = $this->pegawaiRepository->findByUserId($userId);

        if (!$pegawai) {
            return null;
        }

        if ($gaji->pegawai_id !== $pegawai->id) {
            abort(403, 'Akses ditolak. Slip gaji ini bukan milik Anda.');
        }

        $gaji = $this->gajiRepository->loadSlipRelations($gaji);

        $pdf = Pdf::loadView('pegawai.gaji.slip-gaji-pdf', [
            'gaji' => $gaji,
        ]);

        $bulanNama = [
            1 => 'Januari',
            2 => 'Februari',
            12 => 'Desember',
        ];

        $bulanStr = $bulanNama[$gaji->bulan] ?? 'Bulan';

        $namaFile = 'slip-gaji-'
            . $gaji->pegawai->nama
            . '-'
            . $bulanStr
            . '-'
            . $gaji->tahun
            . '.pdf';

        return $pdf->download($namaFile);
    }

    private function getMonthNames(): array
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    }
}