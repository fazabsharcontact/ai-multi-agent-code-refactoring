<?php

namespace App\Providers;

use App\Repositories\Contracts\LaporanPerformaRepositoryInterface;
use App\Repositories\EloquentLaporanPerformaRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            LaporanPerformaRepositoryInterface::class,
            EloquentLaporanPerformaRepository::class
        );
    }

    public function boot(): void
    {
    }
}