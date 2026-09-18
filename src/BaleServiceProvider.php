<?php

declare(strict_types=1);

namespace Sajaddp\Bale;

use Illuminate\Support\ServiceProvider;

class BaleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/bale.php', 'bale');

        $this->app->singleton(BaleClient::class);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/bale.php' => config_path('bale.php'),
        ], 'bale-config');
    }
}
