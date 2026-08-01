<?php

namespace App\Providers;

use App\Repositories\Contracts\DashboardRepositoryInterface;
use App\Repositories\EloquentDashboardRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            DashboardRepositoryInterface::class,
            EloquentDashboardRepository::class
        );
    }

    public function boot(): void
    {
    }
}