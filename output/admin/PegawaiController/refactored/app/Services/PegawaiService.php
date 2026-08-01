<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Repositories\Contracts\DivisiRepositoryInterface;
use App\Repositories\Contracts\JabatanRepositoryInterface;
use App\Repositories\Contracts\PegawaiRepositoryInterface;
use App\Repositories\Contracts\TransactionManagerInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class PegawaiService
{
    public function __construct(
        private readonly PegawaiRepositoryInterface $pegawaiRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly JabatanRepositoryInterface $jabatanRepository,
        private readonly DivisiRepositoryInterface $divisiRepository,
        private readonly TransactionManagerInterface $transactionManager
    ) {
    }

    public function getIndexData(
        ?string $jabatanFilter,
        ?string $search
    ): array {
        return [
            'pegawais' => $this->pegawaiRepository->paginate(
                $jabatanFilter,
                $search,
                10
            ),
            'jabatans' => $this->jabatanRepository->getAllOrderedByName(),
        ];
    }

    public function getCreateData(): array
    {
        return [
            'pegawai' => $this->pegawaiRepository->make(),
            'jabatan' => $this->jabatanRepository->getAllOrderedByName(),
            'divisis' => $this->divisiRepository->getAllWithTims(),
        ];
    }

    public function create(array $validated): void
    {
        $this->transactionManager->transaction(function () use ($validated): void {
            $user = $this->userRepository->create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'pegawai',
            ]);

            $this->pegawaiRepository->createForUser(
                $user,
                $this->pegawaiData($validated)
            );
        });
    }

    public function getEditData(Pegawai $pegawai): array
    {
        return [
            'pegawai' => $this->pegawaiRepository->loadUser($pegawai),
            'jabatan' => $this->jabatanRepository->getAllOrderedByName(),
            'divisis' => $this->divisiRepository->getAllWithTims(),
        ];
    }

    public function update(Pegawai $pegawai, array $validated): void
    {
        $user = $pegawai->user;

        $this->transactionManager->transaction(
            function () use ($validated, $pegawai, $user): void {
                $this->userRepository->update($user, [
                    'username' => $validated['username'],
                    'email' => $validated['email'],
                ]);

                if (array_key_exists('password', $validated)) {
                    $this->userRepository->update($user, [
                        'password' => Hash::make($validated['password']),
                    ]);
                }

                $this->pegawaiRepository->update(
                    $pegawai,
                    $this->pegawaiData($validated)
                );
            }
        );
    }

    public function delete(Pegawai $pegawai): void
    {
        $this->transactionManager->transaction(
            function () use ($pegawai): void {
                $this->pegawaiRepository->deleteRelatedUser($pegawai);
            }
        );
    }

    private function pegawaiData(array $validated): array
    {
        return [
            'jabatan_id' => $validated['jabatan'],
            'tim_id' => $validated['tim_id'] ?? null,
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'no_hp' => $validated['telepon'],
            'alamat' => $validated['alamat'],
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'gaji_pokok' => $validated['gaji'],
            'nama_bank' => $validated['nama_bank'],
            'nomor_rekening' => $validated['nomor_rekening'],
        ];
    }
}