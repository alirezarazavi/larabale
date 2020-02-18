<?php

namespace AlirezaRazavi\LaraBale;

use AlirezaRazavi\LaraBale\Facades\Bale;
use AlirezaRazavi\LaraBale\LaraBale;
use Illuminate\Support\ServiceProvider;

class LaraBaleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([__DIR__ . '/../config/bale.php' => config_path('bale.php')], 'bale-config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/bale.php', 'bale');
        $this->app->bind('bale', function ($app) {
            return new Bale;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [LaraBale::class];
    }
}
