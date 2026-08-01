<?php

namespace App\Services;

use App\Models\Kehadiran;
use App\Models\Pegawai;
use App\Repositories\Contracts\KehadiranRepositoryInterface;
use App\Services\Data\KehadiranActionResult;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class KehadiranService
{
    private const TIMEZONE = 'Asia/Jakarta';

    public function __construct(
        private readonly KehadiranRepositoryInterface $kehadiranRepository
    ) {
    }

    public function getMonthlyKehadiran(
        Pegawai $pegawai,
        mixed $tahun,
        mixed $bulan
    ): LengthAwarePaginator {
        return $this->kehadiranRepository->paginateByPegawaiAndPeriod(
            $pegawai->id,
            $tahun,
            $bulan,
            10
        );
    }

    public function processPresensi(
        Pegawai $pegawai,
        Request $request
    ): KehadiranActionResult {
        $tanggal = Carbon::today(self::TIMEZONE);
        $now = Carbon::now(self::TIMEZONE);

        if ($now->isWeekend()) {
            return $this->result(
                'error',
                'Tidak dapat melakukan presensi pada hari Sabtu atau Minggu.'
            );
        }

        $kehadiranHariIni = $this->kehadiranRepository->findByPegawaiAndDate(
            $pegawai->id,
            $tanggal->toDateString()
        );

        if ($kehadiranHariIni && $kehadiranHariIni->status === 'Cuti') {
            return $this->result(
                'warning',
                'Anda sedang Cuti yang disetujui pada hari ini. Anda tidak perlu melakukan presensi.'
            );
        }

        if (!$kehadiranHariIni) {
            return $this->processNewKehadiran(
                $pegawai,
                $request,
                $tanggal,
                $now
            );
        }

        return $this->processExistingKehadiran($kehadiranHariIni, $now);
    }

    private function processNewKehadiran(
        Pegawai $pegawai,
        Request $request,
        Carbon $tanggal,
        Carbon $now
    ): KehadiranActionResult {
        $status = $request->input('status');

        if ($status === 'Hadir') {
            return $this->recordPresensiMasuk($pegawai, $tanggal, $now);
        }

        if (in_array($status, ['Izin', 'Sakit'])) {
            return $this->recordPengajuan(
                $pegawai,
                $request,
                $tanggal,
                $status
            );
        }

        return $this->result('error', 'Aksi tidak valid.');
    }

    private function recordPresensiMasuk(
        Pegawai $pegawai,
        Carbon $tanggal,
        Carbon $now
    ): KehadiranActionResult {
        $startAbsen = $tanggal->copy()->addHours(8)->addMinutes(50);
        $endHadir = $tanggal->copy()->addHours(9)->addMinutes(15);
        $endTerlambat = $tanggal->copy()->addHours(13);

        if (!$now->between($startAbsen, $endTerlambat)) {
            return $this->result(
                'error',
                'Waktu presensi masuk hanya antara 08:50 - 13:00 WIB.'
            );
        }

        $statusKehadiran = $now->lte($endHadir) ? 'Hadir' : 'Terlambat';

        $this->kehadiranRepository->create([
            'pegawai_id' => $pegawai->id,
            'tanggal' => $tanggal->toDateString(),
            'status' => $statusKehadiran,
            'jam_masuk' => $now->toTimeString(),
        ]);

        return $this->result(
            'success',
            'Presensi masuk berhasil dicatat! Status: ' . $statusKehadiran
        );
    }

    private function recordPengajuan(
        Pegawai $pegawai,
        Request $request,
        Carbon $tanggal,
        string $status
    ): KehadiranActionResult {
        $request->validate([
            'bukti' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $buktiPath = null;

        if ($request->hasFile('bukti')) {
            $buktiPath = $request->file('bukti')
                ->store('bukti_kehadiran', 'public');
        }

        $this->kehadiranRepository->create([
            'pegawai_id' => $pegawai->id,
            'tanggal' => $tanggal->toDateString(),
            'status' => $status,
            'keterangan' => $request->keterangan,
            'bukti' => $buktiPath,
        ]);

        return $this->result(
            'success',
            'Pengajuan ' . $status . ' berhasil dikirim.'
        );
    }

    private function processExistingKehadiran(
        Kehadiran $kehadiran,
        Carbon $now
    ): KehadiranActionResult {
        if (
            in_array($kehadiran->status, ['Hadir', 'Terlambat'])
            && !$kehadiran->jam_pulang
        ) {
            $this->kehadiranRepository->updateJamPulang(
                $kehadiran,
                $now->toTimeString()
            );

            return $this->result(
                'success',
                'Presensi pulang berhasil dicatat!'
            );
        }

        return $this->result(
            'error',
            'Anda sudah melakukan presensi lengkap atau status Anda hari ini adalah '
                . $kehadiran->status
                . '.'
        );
    }

    private function result(
        string $flashKey,
        string $message
    ): KehadiranActionResult {
        return new KehadiranActionResult($flashKey, $message);
    }
}