<?php

namespace App\Providers;

use App\Repositories\Contracts\MasterPotonganRepositoryInterface;
use App\Repositories\EloquentMasterPotonganRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public array $bindings = [
        MasterPotonganRepositoryInterface::class => EloquentMasterPotonganRepository::class,
    ];

    public function register(): void
    {
    }

    public function boot(): void
    {
    }
}