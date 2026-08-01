<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PegawaiRepositoryInterface
{
    public function paginateWithCutiSummary(
        mixed $search,
        int $perPage = 10,
        string $pageName = 'pegawai_page'
    ): LengthAwarePaginator;
}