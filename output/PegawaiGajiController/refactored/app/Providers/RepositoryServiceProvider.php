<?php

namespace App\Providers;

use App\Repositories\Contracts\GajiRepositoryInterface;
use App\Repositories\Contracts\PegawaiRepositoryInterface;
use App\Repositories\EloquentGajiRepository;
use App\Repositories\EloquentPegawaiRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            PegawaiRepositoryInterface::class,
            EloquentPegawaiRepository::class
        );

        $this->app->bind(
            GajiRepositoryInterface::class,
            EloquentGajiRepository::class
        );
    }

    public function boot(): void
    {
    }
}