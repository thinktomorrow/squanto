<?php

namespace Thinktomorrow\SquantoTests\Application\Console;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Thinktomorrow\Squanto\Database\Application\AddDatabaseLine;
use Thinktomorrow\Squanto\Database\DatabaseLinesRepository;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\SquantoTests\TestCase;

class PurgeDatabaseCommandTest extends TestCase
{
    use DatabaseTransactions, DatabaseMigrations;

    /** @var mixed */
    private $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app()->make(DatabaseLinesRepository::class);
    }

    public function test_it_can_purge_disk_lines_to_database()
    {
        // Add obsolete database item
        app(AddDatabaseLine::class)->handle(Line::fromRaw('foo.xxx', []));

        $this->assertTrue($this->repository->exists('foo.xxx'));

        $this->artisan('squanto:purge');

        $this->assertFalse($this->repository->exists('foo.xxx'));
    }
}
