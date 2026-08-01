<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateTugasPengumpulanStatusRequest;
use App\Services\TugasPengumpulanService;
use Illuminate\Http\Request;

class AdminTugasPengumpulanController extends Controller
{
    public function __construct(
        private readonly TugasPengumpulanService $tugasPengumpulanService
    ) {
    }

    /**
     * Menampilkan daftar semua pengumpulan tugas dengan filter.
     */
    public function index(Request $request)
    {
        return view(
            'admin.tugas_pengumpulan.index',
            $this->tugasPengumpulanService->getIndexData($request->all())
        );
    }

    /**
     * Memperbarui status pengumpulan (Terima/Revisi).
     */
    public function updateStatus(UpdateTugasPengumpulanStatusRequest $request, $id)
    {
        $this->tugasPengumpulanService->updateStatus(
            $id,
            $request->status,
            $request->catatan
        );

        return redirect()
            ->back()
            ->with('success', 'Status pengumpulan berhasil diperbarui.');
    }
}