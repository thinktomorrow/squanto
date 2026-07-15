<?php

namespace Thinktomorrow\SquantoTests\Application\Console;

use Thinktomorrow\Squanto\Database\Application\AddDatabaseLine;
use Thinktomorrow\Squanto\Database\DatabaseLinesRepository;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\SquantoTests\TestCase;

class ResetDatabaseCommandTest extends TestCase
{
    private DatabaseLinesRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app()->make(DatabaseLinesRepository::class);
    }

    public function test_it_can_reset_database_lines_for_a_target(): void
    {
        app(AddDatabaseLine::class)->handle(Line::fromRaw('about.title', ['nl' => 'database titel']));
        app(AddDatabaseLine::class)->handle(Line::fromRaw('about.content', ['nl' => 'database inhoud']));

        $this->artisan('squanto:reset about.title --yes');

        $this->assertFalse($this->repository->exists('about.title'));
        $this->assertTrue($this->repository->exists('about.content'));
    }

    public function test_dry_reset_does_not_remove_database_lines(): void
    {
        app(AddDatabaseLine::class)->handle(Line::fromRaw('about.title', ['nl' => 'database titel']));

        $this->artisan('squanto:reset about.title --dry');

        $this->assertTrue($this->repository->exists('about.title'));
    }
}
