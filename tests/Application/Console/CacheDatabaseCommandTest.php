<?php

namespace Thinktomorrow\SquantoTests\Application\Console;

use Thinktomorrow\Squanto\Database\Application\AddDatabaseLine;
use Thinktomorrow\Squanto\Database\DatabaseLinesRepository;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\SquantoTests\TestCase;

class CacheDatabaseCommandTest extends TestCase
{
    /** @var mixed */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app()->make(DatabaseLinesRepository::class);
    }

    public function test_it_can_purge_disk_lines_to_database()
    {
        app(AddDatabaseLine::class)->handle(Line::fromRaw('foo.xxx', ['nl' => 'nl value']));

        $this->artisan('squanto:cache');

        $filepath = config('squanto.cache_path').'/nl/foo.php';
        $cache = require $filepath;

        $this->assertEquals('nl value', $cache['xxx']);
        $this->assertStringContainsString('return [', file_get_contents($filepath));
        $this->assertStringNotContainsString('array (', file_get_contents($filepath));
    }

    public function test_a_null_value_will_not_be_cached()
    {
        app(AddDatabaseLine::class)->handle(Line::fromRaw('foo.xxx', ['nl' => null, 'en' => 'en value']));

        $this->artisan('squanto:cache');

        $this->assertFalse(file_exists(config('squanto.cache_path').'/nl/foo.php'));
        $this->assertTrue(file_exists(config('squanto.cache_path').'/en/foo.php'));
    }

    public function test_it_can_cache_namespaced_database_lines(): void
    {
        app(AddDatabaseLine::class)->handle(Line::fromRaw('chief-form-plugin::general.title', ['nl' => 'Plugin titel']));
        app(AddDatabaseLine::class)->handle(Line::fromRaw('chief-form-plugin::general.nested.content', ['nl' => 'Plugin inhoud']));

        $this->artisan('squanto:cache');

        $filepath = config('squanto.cache_path').'/vendor/chief-form-plugin/nl/general.php';
        $cache = require $filepath;

        $this->assertEquals('Plugin titel', $cache['title']);
        $this->assertEquals('Plugin inhoud', $cache['nested']['content']);
    }
}
