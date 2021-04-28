<img src="https://banners.beyondco.de/Laravel%20Prerender.png?theme=light&packageManager=composer+require&packageName=codebar-ag%2Flaravel-prerender&pattern=circuitBoard&style=style_2&description=Integrate+Prerender.io+with+Laravel&md=1&showWatermark=0&fontSize=175px&images=template&widths=500&heights=500">

[![Latest Version on Packagist](https://img.shields.io/packagist/v/codebar-ag/laravel-prerender.svg?style=flat-square)](https://packagist.org/packages/codebar-ag/laravel-prerender)
[![Total Downloads](https://img.shields.io/packagist/dt/codebar-ag/laravel-prerender.svg?style=flat-square)](https://packagist.org/packages/codebar-ag/laravel-prerender)
[![run-tests](https://github.com/codebar-ag/laravel-prerender/actions/workflows/run-tests.yml/badge.svg)](https://github.com/codebar-ag/laravel-prerender/actions/workflows/run-tests.yml)
[![Check & fix styling](https://github.com/codebar-ag/laravel-prerender/actions/workflows/php-cs-fixer.yml/badge.svg)](https://github.com/codebar-ag/laravel-prerender/actions/workflows/php-cs-fixer.yml)

This package was developed to give you a quick start to integrate with the
Prerender.io service in your Laravel application.

## 🙇 Credits

This package is a clone from [jeroennoten/Laravel-Prerender](https://github.com/jeroennoten/Laravel-Prerender)
with [jeroennoten](https://github.com/jeroennoten) as the original author. 
[CasperLaiTW](https://github.com/CasperLaiTW) provided Laravel 6,7 & 8 
compatibility by an unmerged (14th September 2020) Pull-Request.

## 💡 What is Prerender.io?

The Prerender.io middleware will check each request to see if it's a from a
crawler. If it is a request from a crawler, the middleware will send a request
to Prerender.io for the static HTML of that page. If not, the request will
continue on to your normal server routes. The crawler never knows that you are
using Prerender.io since the response always goes through your server.

> Google now recommends that you use Prerender.io in their 
> [Dynamic Rendering](https://docs.prerender.io/article/9-google-support)
> documentation!



## 🛠 Requirements

- PHP: `^7.2`
- Laravel: `^6`
- Prerender.io access

## ⚙️ Installation

You can install the package via composer:

```shell
composer require codebar-ag/laravel-prerender
```

Apply the Prerender Middleware to your app/Http/Middleware/Kernel.php or routes/web.php

```php
CodebarAg\LaravelPrerender\PrerenderMiddleware;
```

If you want to make use of the Prerender.io service, add the following to your `.env` file:

```dotenv
PRERENDER_TOKEN=token
```

Or if you are using a self-hosted service, add the server address in the `.env` file.

```dotenv
PRERENDER_URL=https://example.com
```

## ✋ Disable the service

You can disable the service by adding the following to your `.env` file:

```dotenv
PRERENDER_ENABLE=false
```

This may be useful for your local development environment.

## ✏️ How it works

1. The middleware checks to make sure we should show a prerendered page
	1. The middleware checks if the request is from a crawler (agent string or `_escaped_fragment_`)
	2. The middleware checks to make sure we aren't requesting a resource (js, css, etc...)
	3. (optional) The middleware checks to make sure the url is in the whitelist
	4. (optional) The middleware checks to make sure the url isn't in the blacklist
2. The middleware makes a `GET` request to the [prerender service](https://github.com/prerender/prerender) (phantomjs server) for the page's prerendered HTML
3. Return the HTML to the crawler

## 🔧 Configuration file

You can publish the config file with:

```shell
php artisan vendor:publish --provider="CodebarAg\LaravelPrerender\LaravelPrerenderServiceProvider"
```

Afterwards you can customize the Whitelist/Blacklist on your own.

### 🤍 Whitelist

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

### 🖤 Blacklist

Blacklist paths to exclude. You can use asterix syntax.
If a blacklist is supplied, all url's will be prerendered except ones containing a blacklist path.
By default, a set of asset extensions are included (this is actually only necessary when you dynamically provide assets via routes).
Note that this is the full request URI, so including starting slash and query parameter string.

```php
// prerender.php:
'blacklist' => [
    '/api/*' // do not prerender pages starting with '/api/'
],
```

## 🚧 Local testing

> Based on the [Getting started](https://docs.prerender.io/article/15-getting-started)
> guide in the Prerender.io documentation.

1. Download and run the prerender Server locally
```shell
git clone https://github.com/prerender/prerender.git
cd prerender
npm clean-install
node server.js
```

The default port is 3000. You can start the node server on a different port
like this:

```shell
PORT=3333 node server.js
```

2. Set the prerender URL:

```dotenv
PRERENDER_URL=http://localhost:3000
```

3. (Optional) Open your browser and visit the following URL. Make sure to
   change `domain.test` to your local domain:

```
http://localhost:3000/render?url=https://domain.test
```

4. Add the `PrerenderMiddleware` to the `web.php` file. We use 
   [Inertia](https://inertiajs.com) in this example:

```php
use CodebarAg\LaravelPrerender\PrerenderMiddleware;

Route::middleware(PrerenderMiddleware::class)->get('/', function () {
    return Inertia::render('Welcome');
});
```

5. Test your page as a crawler. Make sure to change `domain.test` to your local
   domain:

```shell
curl -A Googlebot https://domain.test
```

6. 🎉 You made it. You should see your prerendered html!

## 📝 Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## ✏️ Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## 🧑‍💻 Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## 🎭 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
