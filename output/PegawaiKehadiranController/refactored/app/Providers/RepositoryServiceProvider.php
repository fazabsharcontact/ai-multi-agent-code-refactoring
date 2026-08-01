<?php

namespace App\Providers;

use App\Repositories\Contracts\KehadiranRepositoryInterface;
use App\Repositories\EloquentKehadiranRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public array $bindings = [
        KehadiranRepositoryInterface::class => EloquentKehadiranRepository::class,
    ];

    public function register(): void
    {
    }

    public function boot(): void
    {
    }
}