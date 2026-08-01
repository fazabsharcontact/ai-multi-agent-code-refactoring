<?php

namespace App\Services;

use App\Models\Cuti;
use App\Repositories\Contracts\CutiRepositoryInterface;
use App\Repositories\Contracts\KehadiranRepositoryInterface;
use App\Repositories\Contracts\PegawaiRepositoryInterface;
use App\Repositories\Contracts\SisaCutiRepositoryInterface;
use Carbon\Carbon;

class CutiService
{
    private const STATUS_DIAJUKAN = 'Diajukan';

    private const STATUS_DISETUJUI = 'Disetujui';

    private const STATUS_CUTI = 'Cuti';

    private const JUMLAH_CUTI_TAHUNAN = 12;

    public function __construct(
        private readonly CutiRepositoryInterface $cutiRepository,
        private readonly PegawaiRepositoryInterface $pegawaiRepository,
        private readonly SisaCutiRepositoryInterface $sisaCutiRepository,
        private readonly KehadiranRepositoryInterface $kehadiranRepository
    ) {
    }

    public function getIndexData(mixed $statusFilter, mixed $search): array
    {
        return [
            'cutis' => $this->cutiRepository->paginateWithPegawai(
                $statusFilter,
                $search,
                10,
                'cuti_page'
            ),
            'pegawais' => $this->pegawaiRepository->paginateWithCutiSummary(
                $search,
                10,
                'pegawai_page'
            ),
        ];
    }

    public function updateStatus(Cuti $cuti, mixed $newStatus, mixed $approvedById): ?string
    {
        if ($cuti->status !== self::STATUS_DIAJUKAN) {
            return 'Status cuti ini sudah diproses sebelumnya.';
        }

        if ($newStatus === self::STATUS_DISETUJUI) {
            $error = $this->approveCuti($cuti);

            if ($error !== null) {
                return $error;
            }
        }

        $this->cutiRepository->updateStatus($cuti, $newStatus, $approvedById);

        return null;
    }

    public function resetCutiTahunan(): void
    {
        $this->sisaCutiRepository->resetAll(self::JUMLAH_CUTI_TAHUNAN);
    }

    private function approveCuti(Cuti $cuti): ?string
    {
        $sisaCutiPegawai = $this->sisaCutiRepository->firstOrCreateForPegawai(
            $cuti->pegawai_id
        );

        if ($sisaCutiPegawai->sisa_cuti < $cuti->durasi_hari_kerja) {
            return 'Gagal menyetujui: Sisa cuti pegawai tidak mencukupi.';
        }

        $this->sisaCutiRepository->decrement(
            $sisaCutiPegawai,
            $cuti->durasi_hari_kerja
        );

        $this->integrateCutiWithKehadiran($cuti);

        return null;
    }

    private function integrateCutiWithKehadiran(Cuti $cuti): void
    {
        $currentDate = Carbon::parse($cuti->tanggal_mulai);
        $endDate = Carbon::parse($cuti->tanggal_selesai);

        while ($currentDate->lte($endDate)) {
            if (! $currentDate->isWeekend()) {
                $this->recordKehadiranCuti($cuti, $currentDate->toDateString());
            }

            $currentDate->addDay();
        }
    }

    private function recordKehadiranCuti(Cuti $cuti, string $tanggalCuti): void
    {
        $existingKehadiran = $this->kehadiranRepository->findByPegawaiAndDate(
            $cuti->pegawai_id,
            $tanggalCuti
        );

        if ($existingKehadiran === null) {
            $this->kehadiranRepository->create([
                'pegawai_id' => $cuti->pegawai_id,
                'tanggal' => $tanggalCuti,
                'status' => self::STATUS_CUTI,
                'keterangan' => 'Cuti Disetujui: ' . $cuti->keterangan,
                'jam_masuk' => null,
                'jam_pulang' => null,
                'bukti' => null,
            ]);

            return;
        }

        $this->kehadiranRepository->update($existingKehadiran, [
            'status' => self::STATUS_CUTI,
            'keterangan' => 'Cuti Disetujui (Menggantikan status ' . $existingKehadiran->status . ')',
            'jam_masuk' => null,
            'jam_pulang' => null,
            'bukti' => null,
        ]);
    }
}