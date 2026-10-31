<?php

namespace App\Repositories\Contracts;

use App\Models\Gaji;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface GajiRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 10): LengthAwarePaginator;

    public function create(array $attributes): Gaji;

    public function update(Gaji $gaji, array $attributes): bool;

    public function delete(Gaji $gaji): bool;

    public function deleteDetails(Gaji $gaji): void;

    public function createTunjanganDetails(Gaji $gaji, iterable $tunjangans): void;

    public function createPotonganDetails(Gaji $gaji, iterable $potongans): void;

    public function loadRelations(Gaji $gaji, array $relations): Gaji;

    public function existsForPeriod(int|string $pegawaiId, int|string $bulan, int|string $tahun): bool;

    public function transaction(callable $callback): mixed;
}