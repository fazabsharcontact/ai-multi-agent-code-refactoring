<?php

namespace App\Providers;

use App\Repositories\Contracts\PengumumanPenerimaRepositoryInterface;
use App\Repositories\EloquentPengumumanPenerimaRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            PengumumanPenerimaRepositoryInterface::class,
            EloquentPengumumanPenerimaRepository::class
        );
    }

    public function boot(): void
    {
    }
}