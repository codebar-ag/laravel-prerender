<?php

namespace CodebarAg\LaravelPrerender\Tests;

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

    private function allowSymfonyUserAgent()
    {
        config()->set('prerender.crawler_user_agents', ['symfony']);
    }
}
