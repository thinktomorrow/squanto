<?php

namespace Thinktomorrow\SquantoTests\Application\Console;

use Thinktomorrow\Squanto\Database\Application\AddDatabaseLine;
use Thinktomorrow\Squanto\Database\DatabaseLinesRepository;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\SquantoTests\TestCase;

class PurgeDatabaseCommandTest extends TestCase
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
        // Add obsolete database item
        app(AddDatabaseLine::class)->handle(Line::fromRaw('foo.xxx', []));

        $this->assertTrue($this->repository->exists('foo.xxx'));

        $this->artisan('squanto:purge --yes');

        $this->assertFalse($this->repository->exists('foo.xxx'));
    }

    public function test_dry_purge_does_not_remove_obsolete_lines()
    {
        app(AddDatabaseLine::class)->handle(Line::fromRaw('foo.xxx', []));

        $this->artisan('squanto:purge --dry');

        $this->assertTrue($this->repository->exists('foo.xxx'));
    }
}
