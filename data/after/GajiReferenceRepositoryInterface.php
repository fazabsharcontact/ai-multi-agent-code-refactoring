<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface GajiReferenceRepositoryInterface
{
    public function getAllJabatanOrderedByName(): Collection;

    public function getAllMasterTunjanganOrderedByName(): Collection;

    public function getAllMasterPotonganOrderedByName(): Collection;
}