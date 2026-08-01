<?php

namespace App\Providers;

use App\Repositories\Contracts\MasterTunjanganRepositoryInterface;
use App\Repositories\EloquentMasterTunjanganRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public array $bindings = [
        MasterTunjanganRepositoryInterface::class => EloquentMasterTunjanganRepository::class,
    ];

    public function register(): void
    {
    }

    public function boot(): void
    {
    }
}