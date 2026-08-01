<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePegawaiRequest;
use App\Http\Requests\Admin\UpdatePegawaiRequest;
use App\Models\Pegawai;
use App\Services\PegawaiService;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function __construct(
        private readonly PegawaiService $pegawaiService
    ) {
    }

    public function index(Request $request)
    {
        $data = $this->pegawaiService->getIndexData(
            $request->get('jabatan'),
            $request->get('search')
        );

        return view('admin.pegawai.index', $data);
    }

    public function create()
    {
        return view(
            'admin.pegawai.create',
            $this->pegawaiService->getCreateData()
        );
    }

    public function store(StorePegawaiRequest $request)
    {
        $this->pegawaiService->create($request->validated());

        return redirect()
            ->route('admin.pegawai.index')
            ->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function edit(Pegawai $pegawai)
    {
        return view(
            'admin.pegawai.edit',
            $this->pegawaiService->getEditData($pegawai)
        );
    }

    public function update(
        UpdatePegawaiRequest $request,
        Pegawai $pegawai
    ) {
        $this->pegawaiService->update($pegawai, $request->validated());

        return redirect()
            ->route('admin.pegawai.index')
            ->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy(Pegawai $pegawai)
    {
        $this->pegawaiService->delete($pegawai);

        return redirect()
            ->route('admin.pegawai.index')
            ->with('success', 'Data pegawai berhasil dihapus.');
    }
}