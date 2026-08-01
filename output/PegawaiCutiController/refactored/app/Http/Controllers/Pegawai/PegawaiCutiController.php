<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pegawai\StoreCutiRequest;
use App\Services\PegawaiCutiService;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PegawaiCutiController extends Controller
{
    public function __construct(
        private readonly PegawaiCutiService $cutiService,
        private readonly AuthFactory $auth
    ) {
    }

    public function index(): View
    {
        $data = $this->cutiService->getIndexData(
            $this->auth->guard()->user()
        );

        return view('pegawai.cuti.index', $data);
    }

    public function create(): View
    {
        $data = $this->cutiService->getCreateData(
            $this->auth->guard()->user()
        );

        return view('pegawai.cuti.create', $data);
    }

    public function store(StoreCutiRequest $request): RedirectResponse
    {
        $submitted = $this->cutiService->submit(
            $this->auth->guard()->user(),
            $request->validated()
        );

        if (!$submitted) {
            return back()->with('error', 'Sisa cuti Anda tidak mencukupi.');
        }

        return redirect()
            ->route('pegawai.cuti.index')
            ->with('success', 'Pengajuan cuti berhasil dikirim.');
    }
}