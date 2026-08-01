<?php

namespace App\Providers;

use App\Repositories\Contracts\CutiRepositoryInterface;
use App\Repositories\Contracts\PegawaiRepositoryInterface;
use App\Repositories\EloquentCutiRepository;
use App\Repositories\EloquentPegawaiRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public array $bindings = [
        CutiRepositoryInterface::class => EloquentCutiRepository::class,
        PegawaiRepositoryInterface::class => EloquentPegawaiRepository::class,
    ];

    public function register(): void
    {
    }

    public function boot(): void
    {
    }
}