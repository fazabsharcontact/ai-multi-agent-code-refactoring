<?php

namespace App\Repositories;

use App\Models\Kehadiran;
use App\Repositories\Contracts\KehadiranRepositoryInterface;

class EloquentKehadiranRepository implements KehadiranRepositoryInterface
{
    public function findByPegawaiAndDate(mixed $pegawaiId, string $date): ?Kehadiran
    {
        return Kehadiran::where('pegawai_id', $pegawaiId)
            ->whereDate('tanggal', $date)
            ->first();
    }

    public function create(array $attributes): Kehadiran
    {
        return Kehadiran::create($attributes);
    }

    public function update(Kehadiran $kehadiran, array $attributes): bool
    {
        return $kehadiran->update($attributes);
    }
}