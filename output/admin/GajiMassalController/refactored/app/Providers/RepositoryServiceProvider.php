<?php

namespace App\Providers;

use App\Repositories\Contracts\GajiMassalRepositoryInterface;
use App\Repositories\EloquentGajiMassalRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public array $bindings = [
        GajiMassalRepositoryInterface::class
            => EloquentGajiMassalRepository::class,
    ];

    public function register(): void
    {
    }

    public function boot(): void
    {
    }
}