<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Services\PegawaiPengumumanService;

class PegawaiPengumumanController extends Controller
{
    public function __construct(
        private readonly PegawaiPengumumanService $pegawaiPengumumanService
    ) {
    }

    public function index()
    {
        $pengumumans = $this->pegawaiPengumumanService
            ->getPengumumansForAuthenticatedPegawai();

        return view('pegawai.pengumuman.index', compact('pengumumans'));
    }
}