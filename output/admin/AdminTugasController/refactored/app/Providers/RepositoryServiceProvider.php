<?php

namespace App\Providers;

use App\Repositories\Contracts\AdminTugasRepositoryInterface;
use App\Repositories\EloquentAdminTugasRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public array $bindings = [
        AdminTugasRepositoryInterface::class => EloquentAdminTugasRepository::class,
    ];
}