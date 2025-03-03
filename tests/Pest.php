<?php

use CodebarAg\LaravelPrerender\Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

uses(TestCase::class)->in(__DIR__);

function createMockPrerenderClient(): Client
{
    $mock = new MockHandler([
        new Response(200, ['prerender.io-mock' => 'true'], 'Mocked Prerender Response'),
    ]);

    $stack = HandlerStack::create($mock);

    return new Client(['handler' => $stack]);
}

function createMockTimeoutClient(): Client
{
    $mock = new MockHandler([
        new ConnectException('Could not connect', new Request('GET', 'test')),
    ]);

    $stack = HandlerStack::create($mock);

    return new Client(['handler' => $stack]);
}

function createMockUrlTrackingClient(): Client
{
    $mock = new MockHandler([
        fn (RequestInterface $request) => new Response(
            200,
            ['prerender.io-mock' => 'true'],
            (string) $request->getUri()
        ),
    ]);

    $stack = HandlerStack::create($mock);

    return new Client(['handler' => $stack]);
}

function allowSymfonyUserAgent()
{
    config()->set('prerender.crawler_user_agents', ['symfony']);
}

function allowQueryParams()
{
    config()->set('prerender.full_url', true);
}
