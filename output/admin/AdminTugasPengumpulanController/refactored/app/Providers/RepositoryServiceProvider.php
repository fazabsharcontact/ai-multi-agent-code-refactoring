<?php

namespace App\Providers;

use App\Repositories\Contracts\TugasPengumpulanRepositoryInterface;
use App\Repositories\EloquentTugasPengumpulanRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            TugasPengumpulanRepositoryInterface::class,
            EloquentTugasPengumpulanRepository::class
        );
    }

    public function boot(): void
    {
    }
}