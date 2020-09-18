Laravel | Prerender.io ![Tests](https://github.com/codebar-ag/Laravel-Prerender/workflows/Tests/badge.svg)
=========================== 

## Credits
The Laravel-Prerender package was initially cloned from [jeroennoten/Laravel-Prerender](https://github.com/jeroennoten/Laravel-Prerender). The author was [jeroennoten](https://github.com/jeroennoten). Laravel 6,7 & 8 Support was provided by [CasperLaiTW](https://github.com/CasperLaiTW) via an unmerged (14th September 2020) Pull-Request.

## Laravel Support
This package currently only supports >= Laravel 6 && >= PHP 5.5.9 If you're looking for an older version checkout [jeroennoten/Laravel-Prerender](https://github.com/jeroennoten/Laravel-Prerender).

## Introduction

Google, Facebook, Twitter, Yahoo, and Bing are constantly trying to view your website... but they don't execute javascript. That's why Prerender was built. Prerender is perfect for AngularJS SEO, BackboneJS SEO, EmberJS SEO, and any other javascript framework.

This middleware intercepts requests to your Laravel website or application from crawlers, and then makes a call to the (external) Prerender Service to get the static HTML instead of the javascript for that page.

Prerender adheres to google's `_escaped_fragment_` proposal, which we recommend you use. It's easy:
- Just add `<meta name="fragment" content="!">` to the `<head>` of all of your pages
- If you use hash urls (#), change them to the hash-bang (#!), but you can also use HTML5's push-state
- That's it! Perfect SEO on javascript pages.

## Installation

Require this package run:
```
composer require codebar-ag/laravel-prerender
```

If you want to make use of the prerender.io service, add the following to your `.env` file:

    PRERENDER_TOKEN=yoursecrettoken

If you are using a self-hosted service, add the server address in the `.env` file.

    PRERENDER_URL=http://example.com

You can disable the service by adding the following to your `.env` file:

    PRERENDER_ENABLE=false

This may be useful for your local development environment.

## How it works
1. The middleware checks to make sure we should show a prerendered page
	1. The middleware checks if the request is from a crawler (`_escaped_fragment_` or agent string)
	2. The middleware checks to make sure we aren't requesting a resource (js, css, etc...)
	3. (optional) The middleware checks to make sure the url is in the whitelist
	4. (optional) The middleware checks to make sure the url isn't in the blacklist
2. The middleware makes a `GET` request to the [prerender service](https://github.com/prerender/prerender) (phantomjs server) for the page's prerendered HTML
3. Return the HTML to the crawler

# Customization

To customize the whitelist and the blacklist, you first have to publish the configuration file:

    $ php artisan vendor:publish --provider="CodebarAg\LaravelPrerender\LaravelPrerenderServiceProvider"

### Whitelist

Whitelist paths or patterns. You can use asterix syntax.
If a whitelist is supplied, only url's containing a whitelist path will be prerendered.
An empty array means that all URIs will pass this filter.
Note that this is the full request URI, so including starting slash and query parameter string.

```php
// prerender.php:
'whitelist' => [
    '/frontend/*' // only prerender pages starting with '/frontend/'
],
```

### Blacklist

Blacklist paths to exclude. You can use asterix syntax.
If a blacklist is supplied, all url's will be prerendered except ones containing a blacklist path.
By default, a set of asset extentions are included (this is actually only necessary when you dynamically provide assets via routes).
Note that this is the full request URI, so including starting slash and query parameter string.

```php
// prerender.php:
'blacklist' => [
    '/api/*' // do not prerender pages starting with '/api/'
],
```

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
