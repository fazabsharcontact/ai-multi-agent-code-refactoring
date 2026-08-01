<?php

namespace App\Repositories\Contracts;

use App\Models\Kehadiran;

interface KehadiranRepositoryInterface
{
    public function findByPegawaiAndDate(mixed $pegawaiId, string $date): ?Kehadiran;

    public function create(array $attributes): Kehadiran;

    public function update(Kehadiran $kehadiran, array $attributes): bool;
}