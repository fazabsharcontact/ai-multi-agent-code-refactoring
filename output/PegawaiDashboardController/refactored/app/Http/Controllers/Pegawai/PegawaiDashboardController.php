<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Services\PegawaiDashboardService;
use Illuminate\Support\Facades\Auth;

class PegawaiDashboardController extends Controller
{
    public function __construct(
        private readonly PegawaiDashboardService $dashboardService
    ) {
    }

    public function index()
    {
        $dashboardData = $this->dashboardService->getDashboardData(Auth::id());

        if (!$dashboardData) {
            return redirect()->back()->with(
                'error',
                'Data pegawai Anda tidak ditemukan.'
            );
        }

        return view('pegawai.dashboard', $dashboardData);
    }
}