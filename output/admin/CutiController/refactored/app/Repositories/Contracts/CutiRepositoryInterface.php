<?php

namespace App\Repositories\Contracts;

use App\Models\Cuti;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CutiRepositoryInterface
{
    public function paginateWithPegawai(
        mixed $statusFilter,
        mixed $search,
        int $perPage = 10,
        string $pageName = 'cuti_page'
    ): LengthAwarePaginator;

    public function updateStatus(Cuti $cuti, mixed $status, mixed $approvedById): bool;
}