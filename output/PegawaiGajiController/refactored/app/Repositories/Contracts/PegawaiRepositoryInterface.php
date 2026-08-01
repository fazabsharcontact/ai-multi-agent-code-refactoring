<?php

namespace App\Repositories\Contracts;

use App\Models\Pegawai;

interface PegawaiRepositoryInterface
{
    public function findByUserId(mixed $userId): ?Pegawai;
}