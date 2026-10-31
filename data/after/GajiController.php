<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CekGajiPegawaiRequest;
use App\Http\Requests\Admin\SaveGajiRequest;
use App\Models\Gaji;
use App\Services\GajiService;
use Illuminate\Http\Request;

class GajiController extends Controller
{
    public function __construct(
        private readonly GajiService $gajiService
    ) {
    }

    /**
     * Menampilkan daftar semua data gaji.
     */
    public function index(Request $request)
    {
        $data = $this->gajiService->getIndexData([
            'search' => $request->get('search'),
            'jabatan' => $request->get('jabatan'),
            'bulan' => $request->get('bulan'),
            'tahun' => $request->get('tahun'),
        ]);

        return view('admin.gaji.index', $data);
    }

    /**
     * Menampilkan form untuk membuat data gaji baru.
     */
    public function create()
    {
        return view(
            'admin.gaji.create',
            $this->gajiService->getCreateData()
        );
    }

    /**
     * Menyimpan data gaji baru ke database.
     */
    public function store(SaveGajiRequest $request)
    {
        $this->gajiService->createGaji(
            $this->extractGajiData($request),
            $request->has('tunjangans'),
            $request->has('potongans')
        );

        return redirect()
            ->route('admin.gaji.index')
            ->with('success', 'Data gaji berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit data gaji.
     */
    public function edit(Gaji $gaji)
    {
        return view(
            'admin.gaji.edit',
            $this->gajiService->getEditData($gaji)
        );
    }

    /**
     * Memperbarui data gaji di database.
     */
    public function update(SaveGajiRequest $request, Gaji $gaji)
    {
        $this->gajiService->updateGaji(
            $gaji,
            $this->extractGajiData($request),
            $request->has('tunjangans'),
            $request->has('potongans')
        );

        return redirect()
            ->route('admin.gaji.index')
            ->with('success', 'Data gaji berhasil diperbarui.');
    }

    public function destroy(Gaji $gaji)
    {
        $this->gajiService->deleteGaji($gaji);

        return redirect()
            ->route('admin.gaji.index')
            ->with('success', 'Data gaji berhasil dihapus.');
    }

    public function unduhSlipGaji(Gaji $gaji)
    {
        return $this->gajiService->downloadSlipGaji($gaji);
    }

    public function cekGajiPegawai(CekGajiPegawaiRequest $request)
    {
        return response()->json(
            $this->gajiService->cekGajiPegawai($request->validated())
        );
    }

    private function extractGajiData(SaveGajiRequest $request): array
    {
        return [
            'pegawai_id' => $request->pegawai_id,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'gaji_pokok' => $request->gaji_pokok,
            'tunjangans' => $request->tunjangans,
            'potongans' => $request->potongans,
        ];
    }
}