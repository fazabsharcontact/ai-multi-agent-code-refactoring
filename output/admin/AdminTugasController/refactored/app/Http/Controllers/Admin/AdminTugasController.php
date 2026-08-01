<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexTugasRequest;
use App\Http\Requests\Admin\StoreTugasRequest;
use App\Services\AdminTugasService;
use App\Services\Results\TugasCreationStatus;

class AdminTugasController extends Controller
{
    public function __construct(
        private readonly AdminTugasService $service
    ) {
    }

    public function index(IndexTugasRequest $request)
    {
        return view(
            'admin.tugas.index',
            $this->service->getIndexData($request->filters())
        );
    }

    public function store(StoreTugasRequest $request)
    {
        $status = $this->service->create($request->validated());

        return match ($status) {
            TugasCreationStatus::Success => redirect()
                ->route('admin.tugas.index')
                ->with('success', 'Tugas berhasil ditambahkan.'),

            TugasCreationStatus::MissingPemberi => back()
                ->with(
                    'error',
                    'Gagal membuat tugas: Akun Admin tidak terhubung dengan data pegawai.'
                ),

            TugasCreationStatus::DatabaseError => back()
                ->with(
                    'error',
                    'Terjadi kesalahan pada database. Silakan coba lagi.'
                ),

            TugasCreationStatus::UnexpectedError => back()
                ->with(
                    'error',
                    'Terjadi kesalahan tidak terduga. Silakan coba lagi.'
                ),
        };
    }
}