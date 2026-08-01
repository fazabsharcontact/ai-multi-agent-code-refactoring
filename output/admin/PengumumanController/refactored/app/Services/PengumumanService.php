<?php

namespace App\Services;

use App\Models\Pengumuman;
use App\Repositories\Contracts\PengumumanRepositoryInterface;
use App\Repositories\Contracts\PengumumanTargetRepositoryInterface;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\DatabaseManager;

class PengumumanService
{
    public function __construct(
        private readonly PengumumanRepositoryInterface $pengumumanRepository,
        private readonly PengumumanTargetRepositoryInterface $targetRepository,
        private readonly DatabaseManager $database,
        private readonly AuthFactory $auth
    ) {
    }

    public function getPaginatedPengumumans(): LengthAwarePaginator
    {
        return $this->pengumumanRepository->paginateLatestWithPembuat(10);
    }

    public function getCreationData(): array
    {
        return [
            'pegawais' => $this->targetRepository->getPegawais(),
            'jabatans' => $this->targetRepository->getJabatans(),
            'tims' => $this->targetRepository->getTims(),
            'divisis' => $this->targetRepository->getDivisis(),
        ];
    }

    public function create(array $data): Pengumuman
    {
        return $this->database->transaction(function () use ($data): Pengumuman {
            $pengumuman = $this->pengumumanRepository->create([
                'judul' => $data['judul'],
                'isi' => $data['isi'],
                'user_id' => $this->auth->guard()->id(),
            ]);

            $this->createPenerimas($pengumuman, $data);

            return $pengumuman;
        });
    }

    public function delete(Pengumuman $pengumuman): void
    {
        $this->pengumumanRepository->delete($pengumuman);
    }

    private function createPenerimas(Pengumuman $pengumuman, array $data): void
    {
        if ($data['target_type'] === 'semua') {
            $this->pengumumanRepository->createPenerima($pengumuman, [
                'target_type' => 'semua',
                'target_id' => null,
            ]);

            return;
        }

        if (empty($data['target_ids'])) {
            return;
        }

        foreach ($data['target_ids'] as $targetId) {
            $this->pengumumanRepository->createPenerima($pengumuman, [
                'target_type' => $data['target_type'],
                'target_id' => $targetId,
            ]);
        }
    }
}