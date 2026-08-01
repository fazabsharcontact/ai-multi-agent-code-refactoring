<?php

namespace App\Repositories\Contracts;

use App\Models\MasterPotongan;

interface MasterPotonganRepositoryInterface
{
    public function create(array $attributes): MasterPotongan;

    public function update(MasterPotongan $masterPotongan, array $attributes): bool;

    public function delete(MasterPotongan $masterPotongan): ?bool;
}