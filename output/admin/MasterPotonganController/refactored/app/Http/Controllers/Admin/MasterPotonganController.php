<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMasterPotonganRequest;
use App\Http\Requests\Admin\UpdateMasterPotonganRequest;
use App\Models\MasterPotongan;
use App\Services\MasterPotonganService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class MasterPotonganController extends Controller
{
    public function __construct(
        private readonly MasterPotonganService $masterPotonganService
    ) {
    }

    public function index(): RedirectResponse
    {
        return redirect()->route('admin.tunjangan-potongan.index');
    }

    public function create(): View
    {
        return view('admin.tunjangan.master-potongan.create');
    }

    public function store(StoreMasterPotonganRequest $request): RedirectResponse
    {
        $this->masterPotonganService->create($request->validated());

        return redirect()->route('admin.tunjangan-potongan.index')
            ->with('success', 'Jenis potongan berhasil ditambahkan.');
    }

    public function edit(MasterPotongan $masterPotongan): View
    {
        return view(
            'admin.tunjangan.master-potongan.edit',
            compact('masterPotongan')
        );
    }

    public function update(
        UpdateMasterPotonganRequest $request,
        MasterPotongan $masterPotongan
    ): RedirectResponse {
        $this->masterPotonganService->update(
            $masterPotongan,
            $request->validated()
        );

        return redirect()->route('admin.tunjangan-potongan.index')
            ->with('success', 'Jenis potongan berhasil diperbarui.');
    }

    public function destroy(MasterPotongan $masterPotongan): RedirectResponse
    {
        $this->masterPotonganService->delete($masterPotongan);

        return redirect()->route('admin.tunjangan-potongan.index')
            ->with('success', 'Jenis potongan berhasil dihapus.');
    }
}