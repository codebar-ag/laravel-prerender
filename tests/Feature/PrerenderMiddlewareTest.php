<?php

test('it should prerender page on get request', function () {
    allowSymfonyUserAgent();

    $this->get('/test-middleware')
        ->assertHeader('prerender.io-mock', true)
        ->assertSuccessful();
});

test('it should not prerender page when user agent does not in list', function () {
    $this->get('/test-middleware')
        ->assertHeaderMissing('prerender.io-mock')
        ->assertSee('GET - Success');
});

test('it should prerender page with escaped fragment in query string', function () {
    $this->get('/test-middleware?_escaped_fragment_')
        ->assertHeader('prerender.io-mock', true)
        ->assertSuccessful();
});

test('it should prerender when user agent is part of crawler user agents', function () {
    $this->get('/test-middleware', ['User-Agent' => 'Googlebot/2.1 (+http://www.google.com/bot.html)'])
        ->assertHeader('prerender.io-mock', true)
        ->assertSuccessful();
});

test('it should prerender page with url in whitelist', function () {
    config()->set('prerender.whitelist', ['/test-middleware*']);

    $this->get('/test-middleware?_escaped_fragment_')
        ->assertHeader('prerender.io-mock', true)
        ->assertSuccessful();
});

test('it should not prerender page in blacklist', function () {
    config()->set('prerender.blacklist', ['/test-middleware*']);

    $this->get('/test-middleware?_escaped_fragment_')
        ->assertSuccessful()
        ->assertHeaderMissing('prerender.io-mock')
        ->assertSee('GET - Success');
});

test('it should not prerender page on non-get request', function () {
    allowSymfonyUserAgent();

    $this->post('/test-middleware')
        ->assertSuccessful()
        ->assertSee('Success');
});

test('it should not prerender page when missing user agent', function () {
    $this->get('/test-middleware', ['User-Agent' => null])
        ->assertHeaderMissing('prerender.io-mock')
        ->assertSee('GET - Success');
});

test('it should not prerender page if request times out', function () {
    $this->app->bind(\GuzzleHttp\Client::class, fn () => createMockTimeoutClient());

    allowSymfonyUserAgent();

    $this->get('/test-middleware')
        ->assertHeaderMissing('prerender.io-mock')
        ->assertSee('GET - Success');
});

test('it does not send query strings to prerender by default', function () {
    $this->app->bind(\GuzzleHttp\Client::class, fn () => createMockUrlTrackingClient());

    allowSymfonyUserAgent();

    $this->get('/test-middleware?withQueryParam=true')
        ->assertHeader('prerender.io-mock', true)
        ->assertSuccessful()
        ->assertSee(urlencode('/test-middleware'))
        ->assertDontSee('withQueryParam');
});

test('it sends full query string to prerender', function () {
    $this->app->bind(\GuzzleHttp\Client::class, fn () => createMockUrlTrackingClient());

    allowSymfonyUserAgent();
    allowQueryParams();

    $this->get('/test-middleware?withQueryParam=true')
        ->assertHeader('prerender.io-mock', true)
        ->assertSuccessful()
        ->assertSee(urlencode('/test-middleware?withQueryParam=true'));
});
