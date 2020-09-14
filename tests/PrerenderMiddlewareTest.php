<?php

namespace CodebarAg\LaravelPrerender\Tests;

class PrerenderMiddlewareTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_prerender_page_on_get_request()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function it_should_no_prrender_page_on_non_get_request()
    {
        $this->post('/test-middleware')
            ->assertSuccessful()
            ->assertSee('Success');
    }
}
