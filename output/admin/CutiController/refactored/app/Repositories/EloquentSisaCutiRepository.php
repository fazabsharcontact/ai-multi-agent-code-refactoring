<?php

namespace App\Repositories;

use App\Models\SisaCuti;
use App\Repositories\Contracts\SisaCutiRepositoryInterface;

class EloquentSisaCutiRepository implements SisaCutiRepositoryInterface
{
    public function firstOrCreateForPegawai(mixed $pegawaiId): SisaCuti
    {
        return SisaCuti::firstOrCreate([
            'pegawai_id' => $pegawaiId,
        ]);
    }

    public function decrement(SisaCuti $sisaCuti, mixed $amount): int
    {
        return $sisaCuti->decrement('sisa_cuti', $amount);
    }

    public function resetAll(mixed $amount): int
    {
        return SisaCuti::query()->update([
            'sisa_cuti' => $amount,
        ]);
    }
}