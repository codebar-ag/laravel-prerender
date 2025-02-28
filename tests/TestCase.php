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
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as Orchestra;
use Psr\Http\Message\RequestInterface;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'CodebarAg\\LaravelPrerender\\Database\\Factories\\'.class_basename($modelName).'Factory',
        );
    }

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
