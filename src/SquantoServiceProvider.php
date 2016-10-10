<?php

namespace Thinktomorrow\Squanto;

use League\Flysystem\Filesystem;
use Thinktomorrow\Squanto\Handlers\ClearCacheTranslations;
use Thinktomorrow\Squanto\Handlers\WriteTranslationLineToDisk;
use Illuminate\Translation\TranslationServiceProvider as BaseServiceProvider;
use League\Flysystem\Adapter\Local;
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
        $this->app['translator']->addNamespace('squanto', $this->getSquantoCachePath());
    }

    /**
     * Register our translator
     *
     * @return void
     */
    public function register()
    {
        $this->registerTranslator();

        $this->app->bind(ClearCacheTranslations::class, function($app){
            return new ClearCacheTranslations(
                new Filesystem(new Local($this->getSquantoCachePath()))
            );
        });

        $this->app->bind(WriteTranslationLineToDisk::class, function($app){
            return new WriteTranslationLineToDisk(
                new Filesystem(new Local($this->getSquantoCachePath()))
            );
        });

        $this->app->bind(LaravelTranslationsReader::class, function($app) {
            return new LaravelTranslationsReader(
                new Filesystem(new Local($this->getLangPath()))
            );
        });
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
        return config('squanto.cache_path',storage_path('app/trans'));
    }

    private function getLangPath()
    {
        return config('squanto.lang_path',app('path.lang'));
    }
}
