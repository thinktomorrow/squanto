<?php
declare(strict_types=1);

namespace Thinktomorrow\SquantoTests\Application\Manager;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Thinktomorrow\Squanto\Database\Application\AddDatabaseLine;
use Thinktomorrow\Squanto\Database\DatabaseLinesRepository;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Manager\Pages\Completion;
use Thinktomorrow\SquantoTests\TestCase;

final class CompletionTest extends TestCase
{
    use DatabaseTransactions, DatabaseMigrations;

    /** @var \Thinktomorrow\Squanto\Database\DatabaseLinesRepository */
    private $repository;

    /** @var AddDatabaseLine */
    private $addDatabaseLine;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app()->make(DatabaseLinesRepository::class);
        $this->addDatabaseLine = app()->make(AddDatabaseLine::class);
    }

    /** @test */
    public function it_can_check_if_translations_are_completed_for_a_single_locale()
    {
        $this->addDatabaseLine->handle(Line::fromRaw('foo.first', ['nl' => 'nl value', 'en' => 'en value']));
        $this->addDatabaseLine->handle(Line::fromRaw('foo.second', ['nl' => 'nl value', 'en' => null]));
        $this->addDatabaseLine->handle(Line::fromRaw('foo.third', ['nl' => 'nl value', 'en' => 'en value']));

        $completion = app(Completion::class)->calculate(
            app(DatabaseLinesRepository::class)->all(),
            ['nl']
        );
        $this->assertEquals(100, $completion);

        $completion = app(Completion::class)->calculate(
            app(DatabaseLinesRepository::class)->all(),
            ['en']
        );
        $this->assertEquals(67, $completion);

        $completion = app(Completion::class)->calculate(
            app(DatabaseLinesRepository::class)->all(),
            ['nl','en']
        );
        $this->assertEquals(83, $completion);
    }
}
