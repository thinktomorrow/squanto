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
- Laravel >= 8.0

## Install

Via Composer
``` bash
$ composer require thinktomorrow/squanto
```



## Setup
The service providers of the package will be discovered automatically by Laravel. 
Note that there are two providers: one general package service provider but also a separate one for the Manager browser UI. 

Run the migrations. This will add a table called `squanto_lines` to your database.
``` bash
$ php artisan migrate
```

Next, publish the config file.
```bash 
    php artisan vendor:publish --tag=squanto-config
``` 
In the config, set the different locales you'll wish to maintain via squanto.
Most of the other config settings are sensible defaults and should work fine.

## Admin interface
### routes
Add the following routes. These are the route definitions for viewing and editing the translations. Make sure you'll add the necessary authentication middleware. 
```php 
Route::get('translations/{id}/edit', [\Thinktomorrow\Squanto\Manager\Http\ManagerController::class, 'edit'])->name('squanto.edit');
Route::put('translations/{id}', [\Thinktomorrow\Squanto\Manager\Http\ManagerController::class, 'update'])->name('squanto.update');
Route::get('translations', [\Thinktomorrow\Squanto\Manager\Http\ManagerController::class, 'index'])->name('squanto.index');
```

### Wysiswyg editor
The 'redactor' editor is required so you'll need to include the css and js assets. This is not provided since you'll need a licence.
Feel free to switch editors. The textareas that require a wysiwyg are assigned the `redactor-editor` class.

## Usage

The following console commands are available for the developer.
``` bash
# Push all language lines to the database. Existing database values will remain untouched.
$ php artisan squanto:push

# Remove database lines that are no longer present in the language files.
$ php artisan squanto:purge

# Check if your database lines are up to date, and if a push or purge command is advised.
$ php artisan squanto:check

# Rebuild the database cache.
$ php artisan squanto:cache
```

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
