<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\FacturaRepository;
use App\Repositories\FacturaRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Registrar el Repository en el contenedor
        $this->app->bind(FacturaRepositoryInterface::class, FacturaRepository::class);
    }

    public function boot(): void
    {
        //
    }
}