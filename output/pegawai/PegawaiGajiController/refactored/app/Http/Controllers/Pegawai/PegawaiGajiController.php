<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Gaji;
use App\Services\PegawaiGajiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PegawaiGajiController extends Controller
{
    public function __construct(
        private readonly PegawaiGajiService $pegawaiGajiService
    ) {
    }

    /**
     * Menampilkan riwayat gaji pegawai yang sedang login.
     */
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', Carbon::now()->year);
        $bulan = $request->get('bulan', Carbon::now()->month);

        $data = $this->pegawaiGajiService->getIndexData(
            Auth::id(),
            $tahun,
            $bulan
        );

        if (!$data) {
            return redirect()
                ->back()
                ->with('error', 'Data pegawai Anda tidak ditemukan.');
        }

        return view('pegawai.gaji.index', $data);
    }

    /**
     * Mengunduh slip gaji dalam format PDF.
     *
     * @param \App\Models\Gaji $gaji
     */
    public function unduhSlipGaji(Gaji $gaji)
    {
        $response = $this->pegawaiGajiService->generateSalarySlip(
            Auth::id(),
            $gaji
        );

        if (!$response) {
            return redirect()
                ->back()
                ->with('error', 'Data pegawai Anda tidak ditemukan.');
        }

        return $response;
    }
}