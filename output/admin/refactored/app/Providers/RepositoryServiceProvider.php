<?php

namespace App\Providers;

use App\Repositories\Contracts\DivisiRepositoryInterface;
use App\Repositories\Contracts\TimRepositoryInterface;
use App\Repositories\EloquentDivisiRepository;
use App\Repositories\EloquentTimRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public array $bindings = [
        TimRepositoryInterface::class => EloquentTimRepository::class,
        DivisiRepositoryInterface::class => EloquentDivisiRepository::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //
    }
}