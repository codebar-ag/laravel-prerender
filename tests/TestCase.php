<?php

namespace CodebarAg\LaravelPrerender\Tests;

use CodebarAg\LaravelPrerender\LaravelPrerenderServiceProvider;
use CodebarAg\LaravelPrerender\PrerenderMiddleware;
use GuzzleHttp\Client;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelPrerenderServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app->make(Kernel::class)->prependMiddleware(PrerenderMiddleware::class);

        $this->setupRoutes();

        $app->bind(Client::class, function () {
            return createMockPrerenderClient();
        });
    }

    protected function setupRoutes(): void
    {
        Route::get('test-middleware', function () {
            return response('GET - Success')->header('Content-Type', 'text/plain');
        });

        Route::post('test-middleware', function () {
            return response('Success')->header('Content-Type', 'text/plain');
        });
    }
}
