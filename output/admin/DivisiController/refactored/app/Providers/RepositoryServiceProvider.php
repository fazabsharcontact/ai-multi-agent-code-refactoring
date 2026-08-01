<?php

namespace App\Providers;

use App\Repositories\Contracts\DivisiRepositoryInterface;
use App\Repositories\EloquentDivisiRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            DivisiRepositoryInterface::class,
            EloquentDivisiRepository::class
        );
    }

    public function boot(): void
    {
    }
}