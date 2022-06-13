<?php

namespace Thinktomorrow\SquantoTests\Application\Console;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Thinktomorrow\Squanto\Database\Application\AddDatabaseLine;
use Thinktomorrow\Squanto\Database\DatabaseLinesRepository;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\SquantoTests\TestCase;

class CacheDatabaseCommandTest extends TestCase
{
    use DatabaseTransactions, DatabaseMigrations;

    /** @var mixed */
    private $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app()->make(DatabaseLinesRepository::class);
    }

    /** @test */
    public function it_can_purge_disk_lines_to_database()
    {
        app(AddDatabaseLine::class)->handle(Line::fromRaw('foo.xxx', ['nl' => 'nl value']));

        $this->artisan('squanto:cache');

        $filepath = config('squanto.cache_path') . '/nl/foo.php';
        $cache = require $filepath;

        $this->assertEquals('nl value', $cache['xxx']);
    }

    /** @test */
    public function a_null_value_will_not_be_cached()
    {
        app(AddDatabaseLine::class)->handle(Line::fromRaw('foo.xxx', ['nl' => null, 'en' => 'en value']));

        $this->artisan('squanto:cache');

        $this->assertFalse(file_exists(config('squanto.cache_path') . '/nl/foo.php'));
        $this->assertTrue(file_exists(config('squanto.cache_path') . '/en/foo.php'));
    }
}
