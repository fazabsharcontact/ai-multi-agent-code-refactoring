<?php

namespace App\Repositories\Contracts;

use App\Models\SisaCuti;

interface SisaCutiRepositoryInterface
{
    public function firstOrCreateForPegawai(mixed $pegawaiId): SisaCuti;

    public function decrement(SisaCuti $sisaCuti, mixed $amount): int;

    public function resetAll(mixed $amount): int;
}