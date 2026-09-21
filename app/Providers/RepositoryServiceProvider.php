<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use App\Interfaces\EventRepositoryInterface;
use App\Repositories\EventRepository;
use App\Interfaces\SupportRepositoryInterface;
use App\Repositories\SupportRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            EventRepositoryInterface::class,
            EventRepository::class,
        );

        $this->app->bind(
            SupportRepositoryInterface::class,
            SupportRepository::class,
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
