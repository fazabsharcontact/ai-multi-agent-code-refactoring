<?php

namespace App\Repositories;

use App\Models\Pegawai;
use App\Repositories\Contracts\PegawaiRepositoryInterface;

class EloquentPegawaiRepository implements PegawaiRepositoryInterface
{
    public function findByUserId(mixed $userId): ?Pegawai
    {
        return Pegawai::where('user_id', $userId)->first();
    }
}