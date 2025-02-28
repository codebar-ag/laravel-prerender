<?php

namespace CodebarAg\LaravelPrerender\Tests\Feature;

use CodebarAg\LaravelPrerender\Tests\TestCase;
use GuzzleHttp\Client;

class PrerenderMiddlewareTest extends TestCase
{
    /** @test */
    public function it_should_prerender_page_on_get_request()
    {
        $this->allowSymfonyUserAgent();

        $this->get('/test-middleware')
            ->assertHeader('prerender.io-mock', true)
            ->assertSuccessful();
    }

    /** @test */
    public function it_should_not_prerender_page_when_user_agent_does_not_in_list()
    {
        $this->get('/test-middleware')
            ->assertSuccessful()
            ->assertHeaderMissing('prerender.io-mock')
            ->assertSee('GET - Success');
    }

    /** @test */
    public function it_should_prerender_page_with_escaped_fragment_in_query_string()
    {
        $this->get('/test-middleware?_escaped_fragment_')
            ->assertHeader('prerender.io-mock', true)
            ->assertSuccessful();
    }

    /** @test */
    public function it_should_prerender_when_user_agent_is_part_of_crawler_user_agents()
    {
        $this->get('/test-middleware', ['User-Agent' => 'Googlebot/2.1 (+http://www.google.com/bot.html)'])
            ->assertHeader('prerender.io-mock', true)
            ->assertSuccessful();
    }

    /** @test */
    public function it_should_prerender_page_with_url_in_whitelist()
    {
        config()->set('prerender.whitelist', ['/test-middleware*']);

        $this->get('/test-middleware?_escaped_fragment_')
            ->assertHeader('prerender.io-mock', true)
            ->assertSuccessful();
    }

    /** @test */
    public function is_should_not_prerender_page_in_blacklist()
    {
        config()->set('prerender.blacklist', ['/test-middleware*']);

        $this->get('/test-middleware?_escaped_fragment_')
            ->assertSuccessful()
            ->assertHeaderMissing('prerender.io-mock')
            ->assertSee('GET - Success');
    }

    /** @test */
    public function it_should_not_prerender_page_on_non_get_request()
    {
        $this->allowSymfonyUserAgent();

        $this->post('/test-middleware')
            ->assertSuccessful()
            ->assertSee('Success');
    }

    /** @test */
    public function it_should_not_prerender_page_when_missing_user_agent()
    {
        $this->get('/test-middleware', ['User-Agent' => null])
            ->assertHeaderMissing('prerender.io-mock')
            ->assertSee('GET - Success');
    }

    /** @test */
    public function it_should_not_prerender_page_if_request_times_out()
    {
        $this->app->bind(Client::class, function () {
            return $this->createMockTimeoutClient();
        });

        $this->allowSymfonyUserAgent();

        $this->get('/test-middleware')
            ->assertHeaderMissing('prerender.io-mock')
            ->assertSee('GET - Success');
    }

    /** @test */
    public function it_does_not_send_query_strings_to_prerender_by_default()
    {
        $this->app->bind(Client::class, function () {
            return $this->createMockUrlTrackingClient();
        });

        $this->allowSymfonyUserAgent();

        $this->get('/test-middleware?withQueryParam=true')
            ->assertHeader('prerender.io-mock', true)
            ->assertSuccessful()
            ->assertSee(urlencode('/test-middleware'))
            ->assertDontSee('withQueryParam');
    }

    /** @test */
    public function it_sends_full_query_string_to_prerender()
    {
        $this->app->bind(Client::class, function () {
            return $this->createMockUrlTrackingClient();
        });

        $this->allowSymfonyUserAgent();
        $this->allowQueryParams();

        $this->get('/test-middleware?withQueryParam=true')
            ->assertHeader('prerender.io-mock', true)
            ->assertSuccessful()
            ->assertSee(urlencode('/test-middleware?withQueryParam=true'));
    }

    private function allowSymfonyUserAgent()
    {
        config()->set('prerender.crawler_user_agents', ['symfony']);
    }

    private function allowQueryParams()
    {
        config()->set('prerender.full_url', true);
    }
}
