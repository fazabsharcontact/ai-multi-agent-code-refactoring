<?php

namespace App\Http\Controllers\Admin;

use App\Data\GajiMassalFilterData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GajiMassal\CekGajiSudahAdaRequest;
use App\Http\Requests\Admin\GajiMassal\LangkahDuaGajiMassalRequest;
use App\Http\Requests\Admin\GajiMassal\SimpanGajiMassalRequest;
use App\Services\GajiMassalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GajiMassalController extends Controller
{
    public function __construct(
        private readonly GajiMassalService $gajiMassalService,
    ) {
    }

    public function langkahSatu(Request $request): View
    {
        return view(
            'admin.gaji.gaji-massal-1',
            $this->gajiMassalService->getLangkahSatuData(
                GajiMassalFilterData::fromRequest($request),
            ),
        );
    }

    public function langkahDua(
        LangkahDuaGajiMassalRequest $request,
    ): View {
        $data = $this->gajiMassalService->getLangkahDuaData(
            pegawaiIds: $request->input('pegawai_ids'),
            bulan: $request->input('bulan'),
            tahun: $request->input('tahun'),
        );

        return view('admin.gaji.gaji-massal-2', $data);
    }

    public function simpan(
        SimpanGajiMassalRequest $request,
    ): RedirectResponse {
        $jumlahPegawai = $this->gajiMassalService->simpanGajiMassal(
            validated: $request->validated(),
            tunjangansUmum: $request->tunjangans ?? [],
            potongansUmum: $request->potongans ?? [],
        );

        return redirect()
            ->route('admin.gaji.index')
            ->with(
                'success',
                'Gaji untuk '
                    . $jumlahPegawai
                    . ' pegawai berhasil ditambahkan.',
            );
    }

    public function cekGajiSudahAda(
        CekGajiSudahAdaRequest $request,
    ): JsonResponse {
        $validated = $request->validated();

        $pegawaiSudahGajian = $this->gajiMassalService
            ->getPegawaiSudahGajian(
                pegawaiIds: $validated['pegawai_ids'],
                bulan: $validated['bulan'],
                tahun: $validated['tahun'],
            );

        return response()->json([
            'data' => $pegawaiSudahGajian,
        ]);
    }
}