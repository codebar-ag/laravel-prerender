<?php

namespace CodebarAg\LaravelPrerender;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;

class LaravelPrerenderServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/prerender.php' => config_path('prerender.php'),
        ], 'config');

        if (config('prerender.enable')) {
            /** @var Kernel $kernel */
            $kernel = $this->app->make(Kernel::class);

            $kernel->pushMiddleware(PrerenderMiddleware::class);
        }
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/prerender.php',
            'prerender'
        );
    }
}
