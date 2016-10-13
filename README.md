# squanto

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Manage the static translations of your Laravel application during and after development
Squanto, based on one of the first [Native Indian interpreters](https://nl.wikipedia.org/wiki/Squanto), is an opinionated way to manage the translations in database.

**NOTE: This package is still in development and the api will be subject to change. Please only use this package in production until a stable version is released. That being said, please do try this package out as feedback is much appreciated!**

## Install

Via Composer

``` bash
$ composer require thinktomorrow/squanto
```

## Usage

Publish the migrations and config file:
``` bash
$ php artisan vendor:publish --provider="Thinktomorrow\Squanto\SquantoServiceProvider"
```

Make sure you set the settings in the squanto config file. Especially the locales to be maintained.
Run the migrations
``` bash
$ php artisan migrate
```

Next you can import the existing translations from your lang files by following command:
``` bash
$ php artisan squanto:import
```
If you run this command with the `-dry` option it will simulate the impact of the import first.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email cavensben@gmail.com instead of using the issue tracker.

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
