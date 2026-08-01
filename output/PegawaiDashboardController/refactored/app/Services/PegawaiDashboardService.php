<?php

namespace App\Services;

use App\Repositories\Contracts\PegawaiDashboardRepositoryInterface;
use Carbon\Carbon;

class PegawaiDashboardService
{
    private const STATUS_HADIR = 'Hadir';

    private const STATUS_IZIN = 'Izin';

    private const STATUS_SAKIT = 'Sakit';

    private const STATUS_ABSEN = 'Absen';

    private const STATUS_TERLAMBAT = 'Terlambat';

    private const BULAN = [
        'Jan',
        'Feb',
        'Mar',
        'Apr',
        'Mei',
        'Jun',
        'Jul',
        'Agu',
        'Sep',
        'Okt',
        'Nov',
        'Des',
    ];

    public function __construct(
        private readonly PegawaiDashboardRepositoryInterface $dashboardRepository
    ) {
    }

    public function getDashboardData(mixed $userId): ?array
    {
        $pegawai = $this->dashboardRepository->findPegawaiByUserId($userId);

        if (!$pegawai) {
            return null;
        }

        $pegawaiId = $pegawai->id;
        $bulanSekarang = Carbon::now()->month;
        $tahunSekarang = Carbon::now()->year;

        $totalHariMasuk = $this->getTotalKehadiran(
            $pegawaiId,
            self::STATUS_HADIR,
            $bulanSekarang,
            $tahunSekarang
        );

        $totalIzin = $this->getTotalKehadiran(
            $pegawaiId,
            self::STATUS_IZIN,
            $bulanSekarang,
            $tahunSekarang
        );

        $totalSakit = $this->getTotalKehadiran(
            $pegawaiId,
            self::STATUS_SAKIT,
            $bulanSekarang,
            $tahunSekarang
        );

        $totalAbsen = $this->getTotalKehadiran(
            $pegawaiId,
            self::STATUS_ABSEN,
            $bulanSekarang,
            $tahunSekarang
        );

        $totalTerlambat = $this->getTotalKehadiran(
            $pegawaiId,
            self::STATUS_TERLAMBAT,
            $bulanSekarang,
            $tahunSekarang
        );

        $totalGajiTahun = $this->dashboardRepository->getTotalGajiTahunan(
            $pegawaiId,
            $tahunSekarang
        );

        $gajiPerBulan = $this->dashboardRepository->getGajiPerBulan(
            $pegawaiId,
            $tahunSekarang
        );

        $bulanArray = self::BULAN;
        $dataGaji = $this->buildDataGaji($gajiPerBulan);

        $dataKehadiran = [
            self::STATUS_HADIR => $totalHariMasuk,
            self::STATUS_IZIN => $totalIzin,
            self::STATUS_SAKIT => $totalSakit,
            self::STATUS_ABSEN => $totalAbsen,
            self::STATUS_TERLAMBAT => $totalTerlambat,
        ];

        $pengumumans = $this->dashboardRepository->getPengumumanForPegawai($pegawai);

        return compact(
            'pegawai',
            'totalHariMasuk',
            'totalIzin',
            'totalSakit',
            'totalAbsen',
            'totalTerlambat',
            'totalGajiTahun',
            'bulanArray',
            'dataGaji',
            'dataKehadiran',
            'tahunSekarang',
            'bulanSekarang',
            'pengumumans'
        );
    }

    private function getTotalKehadiran(
        mixed $pegawaiId,
        string $status,
        int $bulan,
        int $tahun
    ): int {
        return $this->dashboardRepository->countKehadiranByStatus(
            $pegawaiId,
            $status,
            $bulan,
            $tahun
        );
    }

    private function buildDataGaji(array $gajiPerBulan): array
    {
        $dataGaji = array_fill(0, 12, 0);

        foreach ($gajiPerBulan as $bulan => $total) {
            $dataGaji[$bulan - 1] = $total;
        }

        return $dataGaji;
    }
}