<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMasterTunjanganRequest;
use App\Http\Requests\Admin\UpdateMasterTunjanganRequest;
use App\Models\MasterTunjangan;
use App\Services\MasterTunjanganService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MasterTunjanganController extends Controller
{
    private const INDEX_ROUTE = 'admin.tunjangan-potongan.index';

    public function __construct(
        private readonly MasterTunjanganService $masterTunjanganService
    ) {
    }

    public function index(): RedirectResponse
    {
        return redirect()->route(self::INDEX_ROUTE);
    }

    public function create(): View
    {
        return view('admin.tunjangan.master-tunjangan.create');
    }

    public function store(StoreMasterTunjanganRequest $request): RedirectResponse
    {
        $this->masterTunjanganService->create($request->validated());

        return $this->redirectToIndex('Jenis tunjangan berhasil ditambahkan.');
    }

    public function edit(MasterTunjangan $masterTunjangan): View
    {
        return view(
            'admin.tunjangan.master-tunjangan.edit',
            compact('masterTunjangan')
        );
    }

    public function update(
        UpdateMasterTunjanganRequest $request,
        MasterTunjangan $masterTunjangan
    ): RedirectResponse {
        $this->masterTunjanganService->update(
            $masterTunjangan,
            $request->validated()
        );

        return $this->redirectToIndex('Jenis tunjangan berhasil diperbarui.');
    }

    public function destroy(MasterTunjangan $masterTunjangan): RedirectResponse
    {
        $this->masterTunjanganService->delete($masterTunjangan);

        return $this->redirectToIndex('Jenis tunjangan berhasil dihapus.');
    }

    private function redirectToIndex(string $message): RedirectResponse
    {
        return redirect()
            ->route(self::INDEX_ROUTE)
            ->with('success', $message);
    }
}