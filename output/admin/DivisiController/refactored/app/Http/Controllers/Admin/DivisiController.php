<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDivisiRequest;
use App\Http\Requests\Admin\UpdateDivisiRequest;
use App\Models\Divisi;
use App\Services\DivisiService;

class DivisiController extends Controller
{
    public function __construct(
        private readonly DivisiService $divisiService
    ) {
    }

    /**
     * Menampilkan form untuk membuat divisi baru.
     */
    public function create()
    {
        return view('admin.timdivisi.divisi-create');
    }

    /**
     * Menyimpan divisi baru ke database.
     */
    public function store(StoreDivisiRequest $request)
    {
        $this->divisiService->create($request->validated());

        return redirect()->route('admin.tim-divisi.index')
            ->with('success', 'Divisi baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit divisi.
     */
    public function edit(Divisi $divisi)
    {
        return view('admin.timdivisi.divisi-edit', compact('divisi'));
    }

    /**
     * Memperbarui data divisi di database.
     */
    public function update(UpdateDivisiRequest $request, Divisi $divisi)
    {
        $this->divisiService->update($divisi, $request->validated());

        return redirect()->route('admin.tim-divisi.index')
            ->with('success', 'Nama divisi berhasil diperbarui.');
    }

    /**
     * Menghapus data divisi dari database.
     */
    public function destroy(Divisi $divisi)
    {
        if (! $this->divisiService->delete($divisi)) {
            return back()->with('error', 'Tidak dapat menghapus divisi yang masih memiliki tim.');
        }

        return redirect()->route('admin.tim-divisi.index')
            ->with('success', 'Divisi berhasil dihapus.');
    }
}