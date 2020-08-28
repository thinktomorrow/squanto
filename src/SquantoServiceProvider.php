<?php

namespace Thinktomorrow\Squanto;

use League\Flysystem\Filesystem;
use Thinktomorrow\Squanto\Console\CheckCommand;
use Illuminate\Contracts\Support\DeferrableProvider;
use Thinktomorrow\Squanto\Disk\Query\ReadLanguageFile;
use Thinktomorrow\Squanto\Disk\Query\ReadMetadataFile;
use Thinktomorrow\Squanto\Console\PurgeDatabaseCommand;
use Thinktomorrow\Squanto\Console\CacheDatabaseCommand;
use Thinktomorrow\Squanto\Console\PushToDatabaseCommand;
use Thinktomorrow\Squanto\Disk\Query\ReadLanguageFolder;
use Thinktomorrow\Squanto\Disk\Query\ReadMetadataFolder;
use Thinktomorrow\Squanto\Database\Query\DatabaseLinesRepository;
use Thinktomorrow\Squanto\Database\Application\CacheDatabaseLines;
use Illuminate\Translation\TranslationServiceProvider as BaseServiceProvider;
use League\Flysystem\Adapter\Local;
use Thinktomorrow\Squanto\Translators\SquantoTranslator;

class SquantoServiceProvider extends BaseServiceProvider implements DeferrableProvider
{
    /**
     * @return array
     */
    public function provides()
    {
        return [
            'translator',
            'translation.loader',
            ReadLanguageFolder::class,
            ReadMetadataFolder::class,
            CacheDatabaseLines::class,
        ];
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom($this->getSquantoCachePath(), 'squanto');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/squanto.php' => config_path('thinktomorrow/squanto.php')
            ], 'squanto-config');

            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

            $this->commands([
                CheckCommand::class,
                CacheDatabaseCommand::class,
                PushToDatabaseCommand::class,
                PurgeDatabaseCommand::class,
            ]);
        }
    }

    /**
     * Register our translator
     *
     * @return void
     */
    public function register()
    {
        $this->app['thinktomorrow.squanto.cache_path'] = $this->getSquantoCachePath();
        $this->app['thinktomorrow.squanto.lang_path'] = $this->getSquantoLangPath();

        $this->registerTranslator();

        $this->app->bind(ReadLanguageFolder::class, function ($app) {
            return new ReadLanguageFolder(
                $app->make(ReadLanguageFile::class),
                new Filesystem(new Local($this->getSquantoLangPath()))
            );
        });

        $this->app->bind(ReadMetadataFolder::class, function ($app) {
            return new ReadMetadataFolder(
                $app->make(ReadMetadataFile::class),
                new Filesystem(new Local($this->getSquantoMetadataPath()))
            );
        });

        $this->app->bind(CacheDatabaseLines::class, function ($app) {
            return new CacheDatabaseLines(
                $app->make(DatabaseLinesRepository::class),
                new Filesystem(new Local($this->getSquantoCachePath()))
            );
        });

        $this->mergeConfigFrom(
            __DIR__.'/../config/squanto.php', 'thinktomorrow.squanto'
        );
    }

    private function registerTranslator()
    {
        $this->registerLoader();

        $this->app->singleton('translator', function ($app) {

            $loader = $app['translation.loader'];
            $locale = $app['config']['app.locale'];

            $trans = new SquantoTranslator($loader, $locale);

            $trans->setFallback($app['config']['app.fallback_locale']);

            // Custom Squanto option to display key or null when translation is not found
            $trans->setKeyAsDefault($app['config']['thinktomorrow.squanto.key_as_default']);

            return $trans;
        });
    }

    private function getSquantoCachePath(): string
    {
        $path = config('thinktomorrow.squanto.cache_path');
        return is_null($path) ? storage_path('app/trans') : $path;
    }

    private function getSquantoLangPath(): string
    {
        $path = config('thinktomorrow.squanto.lang_path');
        return is_null($path) ? app('path.lang') : $path;
    }

    private function getSquantoMetadataPath(): string
    {
        return config('thinktomorrow.squanto.metadata_path', resource_path('squanto_metadata'));
    }
}
