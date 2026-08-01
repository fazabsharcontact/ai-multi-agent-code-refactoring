<?php

namespace App\Providers;

use App\Repositories\Contracts\PengumumanRepositoryInterface;
use App\Repositories\Contracts\PengumumanTargetRepositoryInterface;
use App\Repositories\EloquentPengumumanRepository;
use App\Repositories\EloquentPengumumanTargetRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            PengumumanRepositoryInterface::class,
            EloquentPengumumanRepository::class
        );

        $this->app->bind(
            PengumumanTargetRepositoryInterface::class,
            EloquentPengumumanTargetRepository::class
        );
    }

    public function boot(): void
    {
    }
}