<?php

namespace App\Providers;

use App\Repositories\Contracts\CutiRepositoryInterface;
use App\Repositories\Contracts\KehadiranRepositoryInterface;
use App\Repositories\Contracts\PegawaiRepositoryInterface;
use App\Repositories\Contracts\SisaCutiRepositoryInterface;
use App\Repositories\EloquentCutiRepository;
use App\Repositories\EloquentKehadiranRepository;
use App\Repositories\EloquentPegawaiRepository;
use App\Repositories\EloquentSisaCutiRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public array $bindings = [
        CutiRepositoryInterface::class => EloquentCutiRepository::class,
        PegawaiRepositoryInterface::class => EloquentPegawaiRepository::class,
        SisaCutiRepositoryInterface::class => EloquentSisaCutiRepository::class,
        KehadiranRepositoryInterface::class => EloquentKehadiranRepository::class,
    ];

    public function register(): void
    {
        parent::register();
    }

    public function boot(): void
    {
    }
}