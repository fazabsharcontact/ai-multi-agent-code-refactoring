<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Services\KehadiranService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PegawaiKehadiranController extends Controller
{
    public function __construct(
        private readonly KehadiranService $kehadiranService
    ) {
    }

    public function index(Request $request): View
    {
        $pegawai = Auth::user()->pegawai;

        if (!$pegawai) {
            abort(403, 'Profil pegawai tidak ditemukan.');
        }

        $tahun = $request->get('tahun', now()->year);
        $bulan = $request->get('bulan', now()->month);

        $kehadiran = $this->kehadiranService->getMonthlyKehadiran(
            $pegawai,
            $tahun,
            $bulan
        );

        return view(
            'pegawai.kehadiran.index',
            compact('kehadiran', 'tahun', 'bulan')
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $pegawai = Auth::user()->pegawai;

        if (!$pegawai) {
            return back()->with(
                'error',
                'Profil pegawai tidak ditemukan.'
            );
        }

        $result = $this->kehadiranService->processPresensi(
            $pegawai,
            $request
        );

        return back()->with($result->flashKey, $result->message);
    }
}