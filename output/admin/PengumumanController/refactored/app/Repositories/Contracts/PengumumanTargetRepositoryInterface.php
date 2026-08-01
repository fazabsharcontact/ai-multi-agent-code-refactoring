<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface PengumumanTargetRepositoryInterface
{
    public function getPegawais(): Collection;

    public function getJabatans(): Collection;

    public function getTims(): Collection;

    public function getDivisis(): Collection;
}