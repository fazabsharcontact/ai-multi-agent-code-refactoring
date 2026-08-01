<?php

namespace App\Services;

use App\Repositories\Contracts\KehadiranRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class KehadiranService
{
    public function __construct(
        private readonly KehadiranRepositoryInterface $kehadiranRepository
    ) {
    }

    public function getIndexData($tahun, $bulan, $pegawaiId = null): array
    {
        return [
            'kehadiran' => $this->kehadiranRepository->paginateByPeriod(
                $tahun,
                $bulan,
                $pegawaiId
            ),
            'rekap' => $this->kehadiranRepository->getRekapByPeriod(
                $tahun,
                $bulan,
                $pegawaiId
            ),
            'tahun' => $tahun,
            'bulan' => $bulan,
            'pegawais' => $this->kehadiranRepository->getAllPegawaiForSelection(),
        ];
    }

    public function getShowData($pegawaiId, $tahun, $bulan): array
    {
        $pegawai = $this->kehadiranRepository->findPegawaiWithUserOrFail($pegawaiId);

        return [
            'pegawai' => $pegawai,
            'kehadiran' => $this->kehadiranRepository->getByPegawaiAndPeriod(
                $pegawai->id,
                $tahun,
                $bulan
            ),
            'tahun' => $tahun,
            'bulan' => $bulan,
        ];
    }

    public function downloadBukti($id)
    {
        $kehadiran = $this->kehadiranRepository->findKehadiranOrFail($id);

        if (!$kehadiran->bukti) {
            return redirect()
                ->back()
                ->with('error', 'Tidak ada file bukti untuk absensi ini.');
        }

        $filePath = $kehadiran->bukti;
        $absolutePath = Storage::disk('public')->path($filePath);

        if (!file_exists($absolutePath)) {
            return redirect()
                ->back()
                ->with('error', 'File bukti tidak ditemukan di server.');
        }

        $fileName = basename($filePath);

        return response()->download($absolutePath, $fileName);
    }
}