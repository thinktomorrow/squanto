<?php

namespace Thinktomorrow\Squanto\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Thinktomorrow\Squanto\SquantoServiceProvider;
use Thinktomorrow\Squanto\SquantoManagerServiceProvider;
use Thinktomorrow\Squanto\Tests\DatabaseTransactions;
use Thinktomorrow\Squanto\Tests\TestHelpers;

class TestCase extends BaseTestCase
{
    use DatabaseTransactions,
        TestHelpers;

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
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
        $app['config']->set('app.locale','nl');
        $app['config']->set('app.fallback_locale','en');

        // Dimsav package dependency requires us to set the fallback locale via this config
        // It should if config not set be using the default laravel fallback imo
        $app['config']->set('translatable.fallback_locale','en');
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
