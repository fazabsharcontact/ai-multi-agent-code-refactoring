<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Repositories\Contracts\LaporanPerformaRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class LaporanPerformaService
{
    public function __construct(
        private readonly LaporanPerformaRepositoryInterface $repository
    ) {
    }

    public function getFilterOptions(): array
    {
        return $this->repository->getFilterOptions();
    }

    public function getPerformanceData(
        Request $request,
        bool $paginateKehadiran = true
    ): array {
        [$periode, $tanggalMulai, $tanggalSelesai] = $this->resolvePeriod($request);
        [$filterType, $filterId, $filterTitle] = $this->resolvePegawaiFilter($request);

        $pegawais = $this->repository->getPegawaiPerformance(
            $tanggalMulai,
            $tanggalSelesai,
            $filterType,
            $filterId
        );

        $kehadiranDetails = $this->repository->getKehadiranDetails(
            $pegawais->pluck('id'),
            $tanggalMulai,
            $tanggalSelesai,
            $paginateKehadiran
        );

        $this->calculatePegawaiPerformance($pegawais);

        $totals = $this->calculateTotals($pegawais);
        $chartData = $this->buildChartData($pegawais, $totals);
        $filter = $this->buildFilter(
            $periode,
            $tanggalMulai,
            $tanggalSelesai,
            $filterTitle
        );

        return [
            $pegawais,
            $chartData,
            $filter,
            $totals,
            $kehadiranDetails,
        ];
    }

    private function resolvePeriod(Request $request): array
    {
        $periode = $request->input('periode', 'bulanan');
        $tanggalMulai = Carbon::now()->startOfMonth();
        $tanggalSelesai = Carbon::now()->endOfMonth();

        switch ($periode) {
            case 'harian':
                $tanggalMulai = Carbon::now()->startOfDay();
                $tanggalSelesai = Carbon::now()->endOfDay();
                break;

            case 'mingguan':
                $tanggalMulai = Carbon::now()->startOfWeek();
                $tanggalSelesai = Carbon::now()->endOfWeek();
                break;

            case 'tahunan':
                $tanggalMulai = Carbon::now()->startOfYear();
                $tanggalSelesai = Carbon::now()->endOfYear();
                break;

            case 'custom':
                if (
                    $request->filled('tanggal_mulai')
                    && $request->filled('tanggal_selesai')
                ) {
                    $tanggalMulai = Carbon::parse(
                        $request->input('tanggal_mulai')
                    )->startOfDay();

                    $tanggalSelesai = Carbon::parse(
                        $request->input('tanggal_selesai')
                    )->endOfDay();
                }
                break;
        }

        return [$periode, $tanggalMulai, $tanggalSelesai];
    }

    private function resolvePegawaiFilter(Request $request): array
    {
        if ($request->filled('divisi_id')) {
            $filterId = $request->input('divisi_id');
            $divisi = $this->repository->findDivisi($filterId);
            $filterTitle = $divisi
                ? 'Divisi: '.$divisi->nama_divisi
                : 'Semua Pegawai';

            return ['divisi', $filterId, $filterTitle];
        }

        if ($request->filled('tim_id')) {
            $filterId = $request->input('tim_id');
            $tim = $this->repository->findTim($filterId);
            $filterTitle = $tim
                ? 'Tim: '.$tim->nama_tim
                : 'Semua Pegawai';

            return ['tim', $filterId, $filterTitle];
        }

        if ($request->filled('pegawai_id')) {
            $filterId = $request->input('pegawai_id');
            $pegawai = $this->repository->findPegawai($filterId);
            $filterTitle = $pegawai
                ? 'Pegawai: '.$pegawai->nama
                : 'Semua Pegawai';

            return ['pegawai', $filterId, $filterTitle];
        }

        return [null, null, 'Semua Pegawai'];
    }

    private function calculatePegawaiPerformance(Collection $pegawais): void
    {
        $pegawais->each(function (Pegawai $pegawai): void {
            $pegawai->total_hadir = $pegawai->kehadirans
                ->where('status', 'Hadir')
                ->count();

            $pegawai->total_sakit_izin = $pegawai->kehadirans
                ->whereIn('status', ['Sakit', 'Izin'])
                ->count();

            $pegawai->jumlah_telat = $pegawai->kehadirans
                ->where('status', 'Terlambat')
                ->count();

            $totalHariMasukKerja = $pegawai->total_hadir
                + $pegawai->jumlah_telat;

            $pegawai->persentase_keterlambatan = $totalHariMasukKerja > 0
                ? round(
                    ($pegawai->jumlah_telat / $totalHariMasukKerja) * 100
                )
                : 0;

            $pegawai->total_tugas_diterima = $pegawai->tugasDiterima->count();

            $pegawai->total_tugas_selesai = $pegawai->tugasDiterima
                ->where('status', 'Selesai')
                ->count();
        });
    }

    private function calculateTotals(Collection $pegawais): array
    {
        return [
            'total_hadir' => $pegawais->sum('total_hadir'),
            'total_sakit_izin' => $pegawais->sum('total_sakit_izin'),
            'total_telat' => $pegawais->sum('jumlah_telat'),
            'rata_rata_keterlambatan' => $pegawais->avg(
                'persentase_keterlambatan'
            ),
            'total_tugas_diterima' => $pegawais->sum(
                'total_tugas_diterima'
            ),
            'total_tugas_selesai' => $pegawais->sum(
                'total_tugas_selesai'
            ),
        ];
    }

    private function buildChartData(
        Collection $pegawais,
        array $totals
    ): array {
        return [
            'labels' => $pegawais->pluck('nama'),
            'kehadiran' => $pegawais->pluck('total_hadir'),
            'pieTugas' => [
                'selesai' => $totals['total_tugas_selesai'],
                'belum_selesai' => $totals['total_tugas_diterima']
                    - $totals['total_tugas_selesai'],
            ],
            'pieKeterlambatan' => [
                'tepat_waktu' => $totals['total_hadir'],
                'telat' => $totals['total_telat'],
            ],
            'rataRataWaktuKerja' => $pegawais->map(
                fn (Pegawai $pegawai): array => [
                    $this->calculateAverageTime($pegawai, 'jam_masuk'),
                    $this->calculateAverageTime($pegawai, 'jam_pulang'),
                ]
            ),
        ];
    }

    private function calculateAverageTime(
        Pegawai $pegawai,
        string $attribute
    ): mixed {
        return $pegawai->kehadirans
            ->whereNotNull($attribute)
            ->avg(
                fn ($kehadiran) => Carbon::parse($kehadiran->{$attribute})->hour
                    + Carbon::parse($kehadiran->{$attribute})->minute / 60
            );
    }

    private function buildFilter(
        mixed $periode,
        Carbon $tanggalMulai,
        Carbon $tanggalSelesai,
        string $filterTitle
    ): array {
        return [
            'periode' => $periode,
            'tanggal_mulai' => $tanggalMulai->format('d M Y'),
            'tanggal_selesai' => $tanggalSelesai->format('d M Y'),
            'title' => $filterTitle,
        ];
    }
}