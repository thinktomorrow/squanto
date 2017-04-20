<?php

namespace Thinktomorrow\Squanto;

use League\Flysystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Thinktomorrow\Squanto\Application\Rename\RenameKeysInFiles;
use Thinktomorrow\Squanto\Application\Cache\CachedTranslationFile;
use Thinktomorrow\Squanto\Commands\ImportTranslationsCommand;
use Illuminate\Translation\TranslationServiceProvider as BaseServiceProvider;
use League\Flysystem\Adapter\Local;
use Thinktomorrow\Squanto\Commands\CacheTranslationsCommand;
use Thinktomorrow\Squanto\Commands\RenameKeyCommand;
use Thinktomorrow\Squanto\Services\LaravelTranslationsReader;
use Thinktomorrow\Squanto\Services\LineUsage;
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
            'Thinktomorrow\\Squanto\\Application\\Rename\\RenameKeysInFiles',
            'Thinktomorrow\\Squanto\\Handlers\\ClearCacheTranslations',
            'Thinktomorrow\\Squanto\\Handlers\\WriteTranslationLineToDisk',
            'Thinktomorrow\\Squanto\\Services\\LineUsage',
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
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/squanto.php' => config_path('squanto.php')
            ], 'config');

            if (!class_exists('CreateSquantoTables')) {
                $this->publishes([
                    __DIR__.'/../database/migrations/create_squanto_tables.php' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_squanto_tables.php'),
                ], 'migrations');
            }

            $this->commands([
                ImportTranslationsCommand::class,
                CacheTranslationsCommand::class,
                RenameKeyCommand::class,
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

        $this->app->bind(LineUsage::class, function ($app) {
            return new LineUsage(
                new Finder(),
                array_merge($app['config']->get('view.paths'), [$app['path']])
            );
        });

        $this->app->bind(RenameKeysInFiles::class, function ($app) {
            return new RenameKeysInFiles(
                $app->make(LineUsage::class)
            );
        });

        $this->mergeConfigFrom(__DIR__.'/../config/squanto.php', 'squanto');

        // Load translations in the register method because since 5.4 the boot method for the translation service provider
        // doesn't seem to be triggered by the calls to the translator anymore.
        $this->loadTranslationsFrom($this->getSquantoCachePath(), 'squanto');
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
