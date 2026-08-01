<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LaporanPerformaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LaporanPerformaController extends Controller
{
    public function __construct(
        private readonly LaporanPerformaService $laporanPerformaService
    ) {
    }

    public function index(Request $request)
    {
        $filterOptions = $this->laporanPerformaService->getFilterOptions();

        [
            $pegawais,
            $chartData,
            $filter,
            $totals,
            $kehadiranDetails,
        ] = $this->laporanPerformaService->getPerformanceData($request);

        return view(
            'admin.laporan.performa',
            compact(
                'pegawais',
                'chartData',
                'filter',
                'filterOptions',
                'totals',
                'kehadiranDetails'
            )
        );
    }

    public function unduhPdf(Request $request)
    {
        [
            $pegawais,
            $chartData,
            $filter,
            $totals,
            $kehadiranDetails,
        ] = $this->laporanPerformaService->getPerformanceData($request, false);

        $data = [
            'pegawais' => $pegawais,
            'filter' => $filter,
            'chartData' => $chartData,
            'totals' => $totals,
            'kehadiranDetails' => $kehadiranDetails,
        ];

        $pdf = Pdf::loadView('admin.laporan.performa-pdf', $data)
            ->setPaper('a4', 'landscape');

        $namaFile = 'laporan-performa-'.Str::slug($filter['title']).'.pdf';

        return $pdf->download($namaFile);
    }
}