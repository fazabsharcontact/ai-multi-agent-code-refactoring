<?php

namespace App\Providers;

use App\Repositories\Contracts\GajiReferenceRepositoryInterface;
use App\Repositories\Contracts\GajiRepositoryInterface;
use App\Repositories\Contracts\PegawaiRepositoryInterface;
use App\Repositories\EloquentGajiReferenceRepository;
use App\Repositories\EloquentGajiRepository;
use App\Repositories\EloquentPegawaiRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public array $bindings = [
        GajiRepositoryInterface::class => EloquentGajiRepository::class,
        PegawaiRepositoryInterface::class => EloquentPegawaiRepository::class,
        GajiReferenceRepositoryInterface::class => EloquentGajiReferenceRepository::class,
    ];

    public function register(): void
    {
    }

    public function boot(): void
    {
    }
}