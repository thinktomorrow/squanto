<?php

namespace Thinktomorrow\Squanto;

use League\Flysystem\Filesystem;
use Thinktomorrow\Squanto\Disk\Paths;
use Thinktomorrow\Squanto\Console\CheckCommand;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Illuminate\Contracts\Support\DeferrableProvider;
use Thinktomorrow\Squanto\Disk\ReadLanguageFile;
use Thinktomorrow\Squanto\Disk\ReadMetadataFile;
use Thinktomorrow\Squanto\Console\PurgeDatabaseCommand;
use Thinktomorrow\Squanto\Console\CacheDatabaseCommand;
use Thinktomorrow\Squanto\Console\PushToDatabaseCommand;
use Thinktomorrow\Squanto\Disk\ReadLanguageFolder;
use Thinktomorrow\Squanto\Disk\ReadMetadataFolder;
use Thinktomorrow\Squanto\Database\DatabaseLinesRepository;
use Thinktomorrow\Squanto\Database\Application\CacheDatabaseLines;
use Illuminate\Translation\TranslationServiceProvider as BaseServiceProvider;
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
        $this->loadTranslationsFrom(Paths::getSquantoCachePath(), 'squanto');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/squanto.php' => config_path('squanto.php'),
            ], 'squanto-config');

            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

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
        $this->app['squanto.cache_path'] = Paths::getSquantoCachePath();
        $this->app['squanto.lang_path'] = Paths::getSquantoLangPath();

        $this->registerTranslator();

        $this->app->bind(
            ReadLanguageFolder::class, function ($app) {
            return new ReadLanguageFolder(
                $app->make(ReadLanguageFile::class),
                new Filesystem(new LocalFilesystemAdapter(Paths::getSquantoLangPath()))
            );
        }
        );

        $this->app->bind(
            ReadMetadataFolder::class, function ($app) {
            return new ReadMetadataFolder(
                $app->make(ReadMetadataFile::class),
                new Filesystem(new LocalFilesystemAdapter(Paths::getSquantoMetadataPath()))
            );
        }
        );

        $this->app->bind(
            CacheDatabaseLines::class, function ($app) {
            return new CacheDatabaseLines(
                $app->make(DatabaseLinesRepository::class),
                new Filesystem(new LocalFilesystemAdapter(Paths::getSquantoCachePath()))
            );
        }
        );

        $this->mergeConfigFrom(
            __DIR__ . '/../config/squanto.php', 'squanto'
        );
    }

    private function registerTranslator()
    {
        $this->registerLoader();

        $this->app->singleton(
            'translator', function ($app) {

            $loader = $app['translation.loader'];
            $locale = $app['config']['app.locale'];

            $trans = new SquantoTranslator($loader, $locale);

            $trans->setFallback($app['config']['app.fallback_locale']);

            // Custom Squanto option to display key or null when translation is not found
            $trans->setKeyAsDefault($app['config']['squanto.key_as_default'] ?? false);

            return $trans;
        }
        );
    }
}
