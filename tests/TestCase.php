<?php

namespace CodebarAg\LaravelPrerender\Tests;

use CodebarAg\LaravelPrerender\LaravelPrerenderServiceProvider;
use CodebarAg\LaravelPrerender\PrerenderMiddleware;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Support\Facades\Route;

/**
 * Class TestCase
 *
 * @package CodebarAg\LaravelPrerender\Tests
 */
class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->setupRoutes();

        config()->set('prerender.crawler_user_agents', ['symfony']);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            LaravelPrerenderServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app->make(Kernel::class)->prependMiddleware(PrerenderMiddleware::class);
    }

    /**
     *
     */
    protected function setupRoutes()
    {
        Route::post('test-middleware', function () {
            return 'Success';
        });
    }
}
