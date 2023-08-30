<?php

namespace Thinktomorrow\SquantoTests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Thinktomorrow\Squanto\SquantoManagerServiceProvider;
use Thinktomorrow\Squanto\SquantoServiceProvider;

class TestCase extends BaseTestCase
{
    use DatabaseTransactions, DatabaseMigrations;

    /** @var \Thinktomorrow\Squanto\Translators\SquantoTranslator */
    protected $translator;

    /** @var string */
    protected $langDirectory;

    /** @var string */
    private $tempDirectory;

    use TestHelpers;

    protected function setUp(): void
    {
        parent::setUp();

        // Set language disk directories
        $this->tempDirectory = new TemporaryDirectory($this->getTempDirectory());
        $this->langDirectory = new TemporaryDirectory($this->getTempDirectory('lang'));

        config()->set('squanto.lang_path', $this->getTempDirectory('lang'));
        config()->set('squanto.metadata_path', $this->getTempDirectory('metadata'));

        $this->rebindTranslator();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->tempDirectory->delete();
    }

    protected function getPackageProviders($app)
    {
        return [
            SquantoServiceProvider::class,
            SquantoManagerServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        //copy stubs to temp folder
        $this->recurse_copy($this->getStubDirectory(), $this->getTempDirectory());

        $app['path.lang'] = $this->getTempDirectory('lang');

        // Connection is defined in the phpunit config xml
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => env('DB_DATABASE', __DIR__.'/../database/testing.sqlite'),
            'prefix' => '',
        ]);

        $app['config']->set('database.default', 'testing');
        $app['config']->set('squanto', require $this->getTempDirectory('config/squanto.php'));
        $app['config']->set('app.locale', 'nl');
        $app['config']->set('app.fallback_locale', 'en');

        // Dimsav package dependency requires us to set the fallback locale via this config
        // It should if config not set be using the default laravel fallback imo
        //        $app['config']->set('translatable.locales',['nl','fr','en']);
        //        $app['config']->set('translatable.fallback_locale','en');
    }

    // Register our translator again so any changes on the lang files are reflected in the translator
    protected function rebindTranslator()
    {
        (new SquantoServiceProvider($this->app))->register();

        $this->translator = app('translator');

        //        app()->bind(LaravelTranslationsReader::class, function ($app) {
        //            return new LaravelTranslationsReader(
        //                new Filesystem(new Local($this->getTempDirectory('lang')))
        //            );
        //        });
    }

    private function getStubDirectory($dir = null)
    {
        return __DIR__.'/stubs/' . $dir;
    }

    private function getTempDirectory($dir = null)
    {
        return __DIR__.'/tmp/' . $dir;
    }
}
