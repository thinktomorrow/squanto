<?php

namespace Thinktomorrow\Squanto\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Thinktomorrow\Squanto\SquantoServiceProvider;

class TestCase extends BaseTestCase
{
    private $database = __DIR__.'/tmp/database.sqlite';

    public function setUp()
    {
        parent::setUp();

        $this->migrate();
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
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['path.lang'] = $this->getStubDirectory('lang');

        $this->createTestDatabase();
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => __DIR__.'/tmp/database.sqlite',
            'prefix' => '',
        ]);

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('squanto',require __DIR__.'/stubs/config/squanto.php');
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

    /**
     * Create fresh test database and hydrate with migrations
     */
    public function createTestDatabase()
    {
        // Create new sqlite database for the total of our tests
        $this->removeTestDatabase();

        if(!is_dir(__DIR__.'/tmp')) mkdir('./tests/tmp',0775);

        touch($this->database);
    }

    protected function removeTestDatabase()
    {
        if(file_exists($this->database)) unlink($this->database);
    }

    private function migrate()
    {
        $migrations = [
            'CreateSquantoTables' => 'create_squanto_tables.php',
        ];

        foreach($migrations as $class => $file)
        {
            include_once __DIR__.'/../database/migrations/'.$file;
            (new $class)->up();
        }
    }
}
