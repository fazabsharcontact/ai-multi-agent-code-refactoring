<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveTimRequest;
use App\Models\Tim;
use App\Services\TimService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TimController extends Controller
{
    public function __construct(
        private readonly TimService $timService
    ) {
    }

    /**
     * Menampilkan form untuk membuat tim baru.
     */
    public function create(): View
    {
        $divisis = $this->timService->getDivisis();

        return view('admin.timdivisi.tim-create', compact('divisis'));
    }

    /**
     * Menyimpan tim baru ke database.
     */
    public function store(SaveTimRequest $request): RedirectResponse
    {
        $this->timService->createTim($request->validated());

        return redirect()->route('admin.tim-divisi.index')
            ->with('success', 'Tim baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit tim.
     */
    public function edit(Tim $tim): View
    {
        $divisis = $this->timService->getDivisis();

        return view('admin.timdivisi.tim-edit', compact('tim', 'divisis'));
    }

    /**
     * Memperbarui data tim di database.
     */
    public function update(SaveTimRequest $request, Tim $tim): RedirectResponse
    {
        $this->timService->updateTim($tim, $request->validated());

        return redirect()->route('admin.tim-divisi.index')
            ->with('success', 'Data tim berhasil diperbarui.');
    }

    /**
     * Menghapus data tim dari database.
     */
    public function destroy(Tim $tim): RedirectResponse
    {
        if (! $this->timService->deleteTim($tim)) {
            return back()->with(
                'error',
                'Tidak dapat menghapus tim yang masih memiliki anggota.'
            );
        }

        return redirect()->route('admin.tim-divisi.index')
            ->with('success', 'Tim berhasil dihapus.');
    }
}