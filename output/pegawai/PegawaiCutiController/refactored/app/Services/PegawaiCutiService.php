<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Repositories\Contracts\CutiRepositoryInterface;
use App\Repositories\Contracts\PegawaiRepositoryInterface;
use Illuminate\Contracts\Auth\Authenticatable;

class PegawaiCutiService
{
    private const DEFAULT_SISA_CUTI = 12;

    private const PAGINATION_PER_PAGE = 10;

    private const STATUS_DIAJUKAN = 'Diajukan';

    public function __construct(
        private readonly CutiRepositoryInterface $cutiRepository,
        private readonly PegawaiRepositoryInterface $pegawaiRepository
    ) {
    }

    public function getIndexData(Authenticatable $user): array
    {
        $pegawai = $this->getPegawaiWithSisaCuti($user);

        if (!$pegawai->sisaCuti) {
            $this->pegawaiRepository->createDefaultSisaCuti(
                $pegawai,
                self::DEFAULT_SISA_CUTI
            );

            $pegawai = $this->pegawaiRepository->loadSisaCuti($pegawai);
        }

        $cutis = $this->cutiRepository->paginateByPegawaiId(
            $pegawai->id,
            self::PAGINATION_PER_PAGE
        );

        return compact('cutis', 'pegawai');
    }

    public function getCreateData(Authenticatable $user): array
    {
        $pegawai = $this->getPegawaiWithSisaCuti($user);

        return compact('pegawai');
    }

    public function submit(Authenticatable $user, array $data): bool
    {
        $pegawai = $user->pegawai;
        $sisaCuti = $pegawai->sisaCuti->sisa_cuti ?? 0;

        $durasiCuti = $this->cutiRepository->calculateWorkingDayDuration([
            'tanggal_mulai' => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai'],
        ]);

        if ($sisaCuti < $durasiCuti) {
            return false;
        }

        $this->cutiRepository->create([
            'pegawai_id' => $pegawai->id,
            'tanggal_mulai' => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai'],
            'keterangan' => $data['keterangan'],
            'status' => self::STATUS_DIAJUKAN,
        ]);

        return true;
    }

    private function getPegawaiWithSisaCuti(Authenticatable $user): Pegawai
    {
        return $this->pegawaiRepository->loadSisaCuti($user->pegawai);
    }
}