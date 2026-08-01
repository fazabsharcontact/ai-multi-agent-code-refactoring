<?php

namespace App\Providers;

use App\Repositories\Contracts\PegawaiDashboardRepositoryInterface;
use App\Repositories\EloquentPegawaiDashboardRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            PegawaiDashboardRepositoryInterface::class,
            EloquentPegawaiDashboardRepository::class
        );
    }

    public function boot(): void
    {
    }
}