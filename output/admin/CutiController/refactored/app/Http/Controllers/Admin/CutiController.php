<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateCutiStatusRequest;
use App\Models\Cuti;
use App\Services\CutiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CutiController extends Controller
{
    public function __construct(
        private readonly CutiService $cutiService
    ) {
    }

    /**
     * Menampilkan daftar semua pengajuan cuti dengan filter.
     */
    public function index(Request $request)
    {
        $data = $this->cutiService->getIndexData(
            $request->get('status'),
            $request->get('search')
        );

        $cutis = $data['cutis'];
        $pegawais = $data['pegawais'];

        return view('admin.cuti.index', compact('cutis', 'pegawais'));
    }

    /**
     * Memperbarui status pengajuan cuti (Disetujui / Ditolak).
     */
    public function updateStatus(UpdateCutiStatusRequest $request, Cuti $cuti)
    {
        $newStatus = $request->input('status');

        $error = $this->cutiService->updateStatus(
            $cuti,
            $newStatus,
            Auth::id()
        );

        if ($error !== null) {
            return back()->with('error', $error);
        }

        return redirect()
            ->route('admin.cuti.index')
            ->with('success', "Pengajuan cuti berhasil di-{$newStatus}.");
    }

    /**
     * Mereset sisa cuti tahunan semua pegawai secara manual.
     */
    public function resetCutiTahunan()
    {
        $this->cutiService->resetCutiTahunan();

        return redirect()
            ->route('admin.cuti.index')
            ->with(
                'success',
                'Sisa cuti tahunan semua pegawai berhasil direset menjadi 12.'
            );
    }
}