<?php

namespace App\Repositories\Contracts;

use App\Models\Tim;

interface TimRepositoryInterface
{
    public function create(array $data): Tim;

    public function update(Tim $tim, array $data): bool;

    public function hasPegawais(Tim $tim): bool;

    public function delete(Tim $tim): bool;
}