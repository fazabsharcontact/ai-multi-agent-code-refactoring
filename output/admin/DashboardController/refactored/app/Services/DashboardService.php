<?php

namespace App\Services;

use App\Repositories\Contracts\DashboardRepositoryInterface;
use Carbon\Carbon;

class DashboardService
{
    public function __construct(
        private readonly DashboardRepositoryInterface $dashboardRepository
    ) {
    }

    public function getDashboardData(): array
    {
        $totalPegawai = $this->dashboardRepository->countPegawai();

        $periodeGajiTerbaru = $this->dashboardRepository->getPeriodeGajiTerbaru();

        $totalGaji = 0;

        if ($periodeGajiTerbaru) {
            $totalGaji = $this->dashboardRepository->getTotalGajiByPeriode(
                $periodeGajiTerbaru->tahun,
                $periodeGajiTerbaru->bulan
            );
        }

        $jabatanData = $this->dashboardRepository->getPegawaiPerJabatan();

        $jangkauanWaktu = Carbon::now()->subDays(365);

        $aktivitas = $this->dashboardRepository->getAktivitasTerbaru(
            $jangkauanWaktu,
            5
        );

        $pengumumans = $this->dashboardRepository->getPengumumans(10);

        $meetings = $this->dashboardRepository->getMeetings(10);

        return compact(
            'totalPegawai',
            'totalGaji',
            'jabatanData',
            'aktivitas',
            'pengumumans',
            'meetings'
        );
    }
}