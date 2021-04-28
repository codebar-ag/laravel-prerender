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

If you want to make use of the Prerender.io service, add the following to your `.env` file:

```dotenv
PRERENDER_TOKEN=token
```

Or if you are using a self-hosted service, add the server address in the `.env` file.

```dotenv
PRERENDER_URL=https://prerender.services
```

That's it. Every GET-Request from a crawler will be prerendered.

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

This is the contents of the published config file:

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable Prerender
    |--------------------------------------------------------------------------
    |
    | Set this field to false to fully disable the prerender service. You
    | would probably override this in a local configuration, to disable
    | prerender on your local machine.
    |
    */

    'enable' => env('PRERENDER_ENABLE', true),

    /*
    |--------------------------------------------------------------------------
    | Prerender URL
    |--------------------------------------------------------------------------
    |
    | This is the prerender URL to the service that prerenders the pages.
    | By default, Prerender's hosted service on prerender.io is used
    | (https://service.prerender.io). But you can also set it to your
    | own server address.
    |
    */

    'prerender_url' => env('PRERENDER_URL', 'https://service.prerender.io'),

    /*
    |--------------------------------------------------------------------------
    | Return soft HTTP status codes
    |--------------------------------------------------------------------------
    |
    | By default Prerender returns soft HTTP codes. If you would like it to
    | return the real ones in case of Redirection (3xx) or status Not Found (404),
    | set this parameter to false.
    | Keep in mind that returning real HTTP codes requires appropriate meta tags
    | to be set. For more details, see github.com/prerender/prerender#httpheaders
    |
    */

    'prerender_soft_http_codes' => env('PRERENDER_SOFT_HTTP_STATUS_CODES', true),

    /*
    |--------------------------------------------------------------------------
    | Prerender Token
    |--------------------------------------------------------------------------
    |
    | If you use prerender.io as service, you need to set your prerender.io
    | token here. It will be sent via the X-Prerender-Token header. If
    | you do not provide a token, the header will not be added.
    |
    */

    'prerender_token' => env('PRERENDER_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Prerender Whitelist
    |--------------------------------------------------------------------------
    |
    | Whitelist paths or patterns. You can use asterix syntax, or regular
    | expressions (without start and end markers). If a whitelist is supplied,
    | only url's containing a whitelist path will be prerendered. An empty
    | array means that all URIs will pass this filter. Note that this is the
    | full request URI, so including starting slash and query parameter string.
    | See github.com/JeroenNoten/Laravel-Prerender for an example.
    |
    */

    'whitelist' => [],

    /*
    |--------------------------------------------------------------------------
    | Prerender Blacklist
    |--------------------------------------------------------------------------
    |
    | Blacklist paths to exclude. You can use asterix syntax, or regular
    | expressions (without start and end markers). If a blacklist is supplied,
    | all url's will be prerendered except ones containing a blacklist path.
    | By default, a set of asset extentions are included (this is actually only
    | necessary when you dynamically provide assets via routes). Note that this
    | is the full request URI, so including starting slash and query parameter
    | string. See github.com/JeroenNoten/Laravel-Prerender for an example.
    |
    */

    'blacklist' => [
        '*.js',
        '*.css',
        '*.xml',
        '*.less',
        '*.png',
        '*.jpg',
        '*.jpeg',
        '*.svg',
        '*.gif',
        '*.pdf',
        '*.doc',
        '*.txt',
        '*.ico',
        '*.rss',
        '*.zip',
        '*.mp3',
        '*.rar',
        '*.exe',
        '*.wmv',
        '*.doc',
        '*.avi',
        '*.ppt',
        '*.mpg',
        '*.mpeg',
        '*.tif',
        '*.wav',
        '*.mov',
        '*.psd',
        '*.ai',
        '*.xls',
        '*.mp4',
        '*.m4a',
        '*.swf',
        '*.dat',
        '*.dmg',
        '*.iso',
        '*.flv',
        '*.m4v',
        '*.torrent',
        '*.eot',
        '*.ttf',
        '*.otf',
        '*.woff',
        '*.woff2'
    ],

    /*
    |--------------------------------------------------------------------------
    | Crawler User Agents
    |--------------------------------------------------------------------------
    |
    | Requests from crawlers that do not support _escaped_fragment_ will
    | nevertheless be served with prerendered pages. You can customize
    | the list of crawlers here.
    |
    */

    'crawler_user_agents' => [
        'googlebot',
        'yahoo',
        'bingbot',
        'yandex',
        'baiduspider',
        'facebookexternalhit',
        'twitterbot',
        'rogerbot',
        'linkedinbot',
        'embedly',
        'bufferbot',
        'quora link preview',
        'showyoubot',
        'outbrain',
        'pinterest',
        'pinterest/0.',
        'developers.google.com/+/web/snippet',
        'www.google.com/webmasters/tools/richsnippets',
        'slackbot',
        'vkShare',
        'W3C_Validator',
        'redditbot',
        'Applebot',
        'WhatsApp',
        'flipboard',
        'tumblr',
        'bitlybot',
        'SkypeUriPreview',
        'nuzzel',
        'Discordbot',
        'Google Page Speed',
        'Qwantify'
    ],

];
```

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

4. Test your page as a crawler. Make sure to change `domain.test` to your local
   domain:

```shell
curl -A Googlebot https://domain.test
```

6. 🎉 That's it — you should see the prerendered html!

## 📝 Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## ✏️ Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## 🧑‍💻 Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## 🎭 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
