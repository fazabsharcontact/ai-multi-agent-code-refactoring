<?php

namespace App\Providers;

use App\Repositories\Contracts\JabatanRepositoryInterface;
use App\Repositories\EloquentJabatanRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public array $bindings = [
        JabatanRepositoryInterface::class => EloquentJabatanRepository::class,
    ];

    public function register(): void
    {
        parent::register();
    }

    public function boot(): void
    {
    }
}