<?php

namespace CodebarAg\LaravelPrerender\Tests;

use CodebarAg\LaravelPrerender\LaravelPrerenderServiceProvider;
use CodebarAg\LaravelPrerender\PrerenderMiddleware;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Support\Facades\Route;
use Psr\Http\Message\RequestInterface;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setupRoutes();
    }

    /**
     * @param  Application  $app
     */
    protected function getPackageProviders($app): array
    {
        return [
            LaravelPrerenderServiceProvider::class,
        ];
    }

    /**
     * @param  Application  $app
     */
    protected function getEnvironmentSetUp($app): void
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

    protected function createMockTimeoutClient(): Client
    {
        $mock = new MockHandler([
            new ConnectException('Could not connect', new Request('GET', 'test')),
        ]);

        $stack = HandlerStack::create($mock);

        return new Client(['handler' => $stack]);
    }

    protected function createMockUrlTrackingClient(): Client
    {
        $mock = new MockHandler([
            function (RequestInterface $request) {
                return new Response(
                    200,
                    ['prerender.io-mock' => true],
                    (string) $request->getUri()
                );
            },
        ]);

        $stack = HandlerStack::create($mock);

        return new Client(['handler' => $stack]);
    }

    protected function setupRoutes(): void
    {
        Route::get('test-middleware', function () {
            return 'GET - Success';
        });

        Route::post('test-middleware', function () {
            return 'Success';
        });
    }
}
