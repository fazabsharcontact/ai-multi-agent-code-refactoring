<?php

namespace App\Services;

use App\Repositories\Contracts\TugasPengumpulanRepositoryInterface;

class TugasPengumpulanService
{
    private const STATUSES = [
        'pending',
        'diterima',
        'revisi',
    ];

    public function __construct(
        private readonly TugasPengumpulanRepositoryInterface $tugasPengumpulanRepository
    ) {
    }

    public function getIndexData(array $filters): array
    {
        return [
            'pengumpulan' => $this->tugasPengumpulanRepository->paginateWithFilters($filters, 15),
            'jabatans' => $this->tugasPengumpulanRepository->getAllJabatansOrderedByName(),
            'tims' => $this->tugasPengumpulanRepository->getAllTimsOrderedByName(),
            'divisis' => $this->tugasPengumpulanRepository->getAllDivisisOrderedByName(),
            'statuses' => self::STATUSES,
        ];
    }

    public function updateStatus(mixed $id, string $status, ?string $catatan): void
    {
        $pengumpulan = $this->tugasPengumpulanRepository->findOrFail($id);

        $pengumpulan->status = $status;
        $pengumpulan->catatan = $status === 'revisi' ? $catatan : null;

        $this->tugasPengumpulanRepository->saveTugasPengumpulan($pengumpulan);

        $tugas = $pengumpulan->tugas;

        if ($tugas) {
            if ($status === 'diterima') {
                $tugas->status = 'Selesai';
            } elseif ($status === 'revisi') {
                $tugas->status = 'Dikerjakan';
            }

            $this->tugasPengumpulanRepository->saveTugas($tugas);
        }
    }
}