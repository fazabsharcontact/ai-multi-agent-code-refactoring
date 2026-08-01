<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreJabatanRequest;
use App\Http\Requests\Admin\UpdateJabatanRequest;
use App\Models\Jabatan;
use App\Services\JabatanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JabatanController extends Controller
{
    public function __construct(
        private readonly JabatanService $jabatanService
    ) {
    }

    /**
     * Menampilkan daftar semua data jabatan dengan filter.
     */
    public function index(Request $request): View
    {
        $jabatans = $this->jabatanService->getFilteredJabatans(
            $request->get('search'),
            $request->get('jabatan')
        );

        return view('admin.jabatan.index', compact('jabatans'));
    }

    /**
     * Menampilkan form untuk membuat jabatan baru.
     */
    public function create(): View
    {
        return view('admin.jabatan.create');
    }

    /**
     * Menyimpan jabatan baru ke database.
     */
    public function store(StoreJabatanRequest $request): RedirectResponse
    {
        $this->jabatanService->createJabatan($request->validated());

        return redirect()->route('admin.jabatan.index')
            ->with('success', 'Jabatan berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit jabatan.
     */
    public function edit(Jabatan $jabatan): View
    {
        return view('admin.jabatan.edit', compact('jabatan'));
    }

    /**
     * Memperbarui data jabatan di database.
     */
    public function update(
        UpdateJabatanRequest $request,
        Jabatan $jabatan
    ): RedirectResponse {
        $this->jabatanService->updateJabatan(
            $jabatan,
            $request->validated()
        );

        return redirect()->route('admin.jabatan.index')
            ->with('success', 'Jabatan berhasil diperbarui.');
    }

    /**
     * Menghapus data jabatan dari database.
     */
    public function destroy(Jabatan $jabatan): RedirectResponse
    {
        $this->jabatanService->deleteJabatan($jabatan);

        return redirect()->route('admin.jabatan.index')
            ->with('success', 'Jabatan berhasil dihapus.');
    }
}