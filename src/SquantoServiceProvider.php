<?php

namespace Thinktomorrow\Squanto;

use League\Flysystem\Filesystem;
use Thinktomorrow\Squanto\Services\CachedTranslationFile;
use Thinktomorrow\Squanto\Import\ImportTranslationsCommand;
use Illuminate\Translation\TranslationServiceProvider as BaseServiceProvider;
use League\Flysystem\Adapter\Local;
use Thinktomorrow\Squanto\Services\CacheTranslationsCommand;
use Thinktomorrow\Squanto\Services\LaravelTranslationsReader;
use Thinktomorrow\Squanto\Translators\SquantoTranslator;

class SquantoServiceProvider extends BaseServiceProvider
{
    protected $defer = true;

    /**
     * @return array
     */
    public function provides()
    {
        return [
            'translator',
            'translation.loader',
            'Thinktomorrow\\Squanto\\Handlers\\ClearCacheTranslations',
            'Thinktomorrow\\Squanto\\Handlers\\WriteTranslationLineToDisk',
            'Thinktomorrow\\Squanto\\Services\\LaravelTranslationsReader',
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
                __DIR__.'/../config/squanto.php' => config_path('squanto.php')
            ], 'config');

            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

            $this->commands([
                ImportTranslationsCommand::class,
                CacheTranslationsCommand::class,
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
        $this->app['squanto.cache_path'] = $this->getSquantoCachePath();
        $this->app['squanto.lang_path'] = $this->getSquantoLangPath();

        $this->registerTranslator();

        $this->app->bind(CachedTranslationFile::class, function ($app) {
            return new CachedTranslationFile(
                new Filesystem(new Local($this->getSquantoCachePath()))
            );
        });

        $this->app->bind(LaravelTranslationsReader::class, function ($app) {
            return new LaravelTranslationsReader(
                new Filesystem(new Local($this->getSquantoLangPath()))
            );
        });

        $this->publishes([
            __DIR__.'/../config/squanto.php' => config_path('thinktomorrow/squanto.php'),
        ]);
        
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
            $trans->setKeyAsDefault($app['config']['squanto.key_as_default']);

            return $trans;
        });
    }

    private function getSquantoCachePath()
    {
        $path = config('squanto.cache_path');
        return is_null($path) ? storage_path('app/trans') : $path;
    }

    private function getSquantoLangPath()
    {
        $path = config('squanto.lang_path');
        return is_null($path) ? app('path.lang') : $path;
    }
}
