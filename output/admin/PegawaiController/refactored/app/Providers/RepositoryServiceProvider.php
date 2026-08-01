<?php

namespace App\Providers;

use App\Repositories\Contracts\DivisiRepositoryInterface;
use App\Repositories\Contracts\JabatanRepositoryInterface;
use App\Repositories\Contracts\PegawaiRepositoryInterface;
use App\Repositories\Contracts\TransactionManagerInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\DatabaseTransactionManager;
use App\Repositories\EloquentDivisiRepository;
use App\Repositories\EloquentJabatanRepository;
use App\Repositories\EloquentPegawaiRepository;
use App\Repositories\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public array $bindings = [
        PegawaiRepositoryInterface::class => EloquentPegawaiRepository::class,
        UserRepositoryInterface::class => EloquentUserRepository::class,
        JabatanRepositoryInterface::class => EloquentJabatanRepository::class,
        DivisiRepositoryInterface::class => EloquentDivisiRepository::class,
        TransactionManagerInterface::class => DatabaseTransactionManager::class,
    ];

    public function register(): void
    {
    }

    public function boot(): void
    {
    }
}