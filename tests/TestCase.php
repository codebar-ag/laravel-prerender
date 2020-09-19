<?php

namespace CodebarAg\LaravelPrerender\Tests;

use CodebarAg\LaravelPrerender\LaravelPrerenderServiceProvider;
use CodebarAg\LaravelPrerender\PrerenderMiddleware;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
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

        // mock guzzle client
        $app->bind(Client::class, function () {
            $mock = new MockHandler([
                new Response(200, ['prerender.io-mock' => true]),
            ]);
            $stack = HandlerStack::create($mock);

            return new Client(['handler' => $stack]);
        });
    }

    /**
     *
     */
    protected function setupRoutes()
    {
        Route::get('test-middleware', function () {
            return 'GET - Success';
        });

        Route::post('test-middleware', function () {
            return 'Success';
        });
    }
}
