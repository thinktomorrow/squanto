# squanto

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Manage the static translations of your Laravel application during and after development.
Squanto, its name derived from one of the first [Native Indian interpreters](https://nl.wikipedia.org/wiki/Squanto), is an opinionated way to manage the translations in database.

**NOTE: This package is still in development and the api will be subject to change. That being said, please do try this package out as feedback is much appreciated!**

## Requirements
- php >= 7.4.7
- Laravel >= 7.15

## Install

Via Composer
``` bash
$ composer require thinktomorrow/squanto
```



## Setup
The service providers of the package will be discovered automatically. Make note that there are two providers: one for the package but also a separate service provider for the Manager browser UI. 

Publish the config file:
```bash 
    php artisan vendor:publish --tag=squanto-config
``` 


2. Basic development protection
Add the `ThinkTomorrow\Squanto\Manager\ManagesSquanto` trait to your User model. This will expose a public
method 'isSquantoDeveloper' to be used inside your views and middleware.

3. Managing squanto via the interface also requires a middleware `squanto.develop` which should protect 
the routes responsible for adding, editing and deleting translation lines. An insecure default is 
available but for production you must setup your own permissions logic on these routes.
    ``` php
    protected $routeMiddleware = [
        'squanto.developer' => \Thinktomorrow\Squanto\Manager\Http\Middleware\Developer::class,
    ],
    ```

4. Add the service provider in your config/app.php providers array
    ``` php
    'providers' => [
        ...
        Thinktomorrow\Squanto\SquantoServiceProvider::class,
        Thinktomorrow\Squanto\SquantoManagerServiceProvider::class, // Optionally add the UI manager
    ];
    ```

5. Editor

The 'redactor' editor is required so you'll need to include the css and js assets. This is not provided since you'll need a licence.
Feel free to switch editors. The textareas that require a wysiwyg are assigned the `redactor-editor` class.

## Usage

Make sure you set the settings in the squanto config file. Especially the locales to be maintained.
Run the migrations
``` bash
$ php artisan migrate
```

Next you can import the existing translations from your lang files with the following command:
``` bash
$ php artisan squanto:import
```
If you run this command with the `-dry` option it will simulate the impact of the import first.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email to dev@thinktomorrow.be instead of using the issue tracker.

## Credits

- [Ben Cavens][link-author]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/thinktomorrow/squanto.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/thinktomorrow/squanto/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/thinktomorrow/squanto.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/thinktomorrow/squanto.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/thinktomorrow/squanto.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/thinktomorrow/squanto
[link-travis]: https://travis-ci.org/thinktomorrow/squanto
[link-scrutinizer]: https://scrutinizer-ci.com/g/thinktomorrow/squanto/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/thinktomorrow/squanto
[link-downloads]: https://packagist.org/packages/thinktomorrow/squanto
[link-author]: https://github.com/bencavens
[link-contributors]: ../../contributors
