<?php

namespace App\Services;

use App\Repositories\Contracts\AdminTugasRepositoryInterface;
use App\Services\Results\TugasCreationStatus;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminTugasService
{
    private const STATUSES = [
        'Baru',
        'Dikerjakan',
        'Ditinjau',
        'Selesai',
    ];

    public function __construct(
        private readonly AdminTugasRepositoryInterface $repository
    ) {
    }

    public function getIndexData(array $filters): array
    {
        return [
            'tugas' => $this->repository->paginate($filters, 15),
            'pegawais' => $this->repository->getPegawais(),
            'jabatans' => $this->repository->getJabatans(),
            'tims' => $this->repository->getTims(),
            'divisis' => $this->repository->getDivisis(),
            'statuses' => self::STATUSES,
        ];
    }

    public function create(array $data): TugasCreationStatus
    {
        $pemberi = Auth::user()->pegawai;

        if (!$pemberi) {
            return TugasCreationStatus::MissingPemberi;
        }

        try {
            $this->repository->create([
                'judul_tugas' => $data['judul_tugas'],
                'deskripsi' => $data['deskripsi'] ?? null,
                'pemberi_id' => $pemberi->id,
                'penerima_id' => $data['penerima_id'],
                'tenggat_waktu' => $data['tenggat_waktu'],
                'status' => 'Baru',
            ]);

            return TugasCreationStatus::Success;
        } catch (QueryException $exception) {
            Log::error(
                'Error saat membuat tugas (QueryException): '.$exception->getMessage()
            );

            return TugasCreationStatus::DatabaseError;
        } catch (\Exception $exception) {
            Log::error(
                'Error umum saat membuat tugas: '.$exception->getMessage()
            );

            return TugasCreationStatus::UnexpectedError;
        }
    }
}