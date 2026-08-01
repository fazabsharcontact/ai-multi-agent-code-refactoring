<?php

namespace App\Providers;

use App\Repositories\Contracts\MeetingRepositoryInterface;
use App\Repositories\Contracts\PegawaiRepositoryInterface;
use App\Repositories\EloquentMeetingRepository;
use App\Repositories\EloquentPegawaiRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            MeetingRepositoryInterface::class,
            EloquentMeetingRepository::class
        );

        $this->app->bind(
            PegawaiRepositoryInterface::class,
            EloquentPegawaiRepository::class
        );
    }

    public function boot(): void
    {
    }
}