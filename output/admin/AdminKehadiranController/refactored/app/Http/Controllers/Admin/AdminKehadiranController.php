<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\KehadiranService;
use Illuminate\Http\Request;

class AdminKehadiranController extends Controller
{
    public function __construct(
        private readonly KehadiranService $kehadiranService
    ) {
    }

    /**
     * Menampilkan daftar kehadiran dengan filter dan paginasi.
     */
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);
        $bulan = $request->get('bulan', now()->month);

        $data = $this->kehadiranService->getIndexData(
            $tahun,
            $bulan,
            $request->pegawai_id
        );

        return view('admin.kehadiran.index', $data);
    }

    /**
     * Menampilkan detail kehadiran per pegawai.
     */
    public function show($pegawaiId, Request $request)
    {
        $tahun = $request->get('tahun', now()->year);
        $bulan = $request->get('bulan', now()->month);

        $data = $this->kehadiranService->getShowData(
            $pegawaiId,
            $tahun,
            $bulan
        );

        return view('admin.kehadiran.show', $data);
    }

    /**
     * Mengunduh file bukti kehadiran (Izin/Sakit).
     */
    public function downloadBukti($id)
    {
        return $this->kehadiranService->downloadBukti($id);
    }
}