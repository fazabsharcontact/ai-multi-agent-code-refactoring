<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePengumumanRequest;
use App\Models\Pengumuman;
use App\Services\PengumumanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PengumumanController extends Controller
{
    public function __construct(
        private readonly PengumumanService $pengumumanService
    ) {
    }

    /**
     * Menampilkan daftar semua pengumuman.
     */
    public function index(): View
    {
        $pengumumans = $this->pengumumanService->getPaginatedPengumumans();

        return view('admin.pengumuman.index', compact('pengumumans'));
    }

    /**
     * Menampilkan form untuk membuat pengumuman baru.
     */
    public function create(): View
    {
        return view(
            'admin.pengumuman.create',
            $this->pengumumanService->getCreationData()
        );
    }

    /**
     * Menyimpan pengumuman baru ke database.
     */
    public function store(StorePengumumanRequest $request): RedirectResponse
    {
        $this->pengumumanService->create($request->validated());

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil dipublikasikan.');
    }

    /**
     * Menghapus pengumuman.
     */
    public function destroy(Pengumuman $pengumuman): RedirectResponse
    {
        $this->pengumumanService->delete($pengumuman);

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }
}