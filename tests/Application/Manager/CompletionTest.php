<?php
declare(strict_types=1);

namespace Thinktomorrow\SquantoTests\Application\Manager;

use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\SquantoTests\TestCase;
use Thinktomorrow\Squanto\Manager\Completion;
use Thinktomorrow\Squanto\Database\DatabaseLine;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Thinktomorrow\Squanto\Database\Application\AddDatabaseLine;
use Thinktomorrow\Squanto\Database\Query\DatabaseLinesRepository;

final class CompletionTest extends TestCase
{
    use DatabaseTransactions, DatabaseMigrations;

    /** @var DatabaseLinesRepository */
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

        $completion = Completion::fromDatabaseLines(DatabaseLine::all());

        $this->assertFalse($completion->isComplete('en'));
        $this->assertTrue($completion->isComplete('nl'));
    }

    /** @test */
    public function it_can_check_if_translations_are_incomplete_for_all_locales()
    {
        config()->set('thinktomorrow.squanto.locales', ['nl', 'en']);

        $this->addDatabaseLine->handle(Line::fromRaw('foo.first', ['nl' => 'nl value', 'en' => 'en value']));
        $this->addDatabaseLine->handle(Line::fromRaw('foo.second', ['nl' => 'nl value', 'en' => null]));
        $this->addDatabaseLine->handle(Line::fromRaw('foo.third', ['nl' => 'nl value', 'en' => 'en value']));

        $completion = Completion::fromDatabaseLines(DatabaseLine::all());

        $this->assertFalse($completion->isCompleteForAllLocales());
    }

    /** @test */
    public function it_can_check_if_translations_are_completed_for_all_locales()
    {
        config()->set('thinktomorrow.squanto.locales', ['nl', 'en']);

        $this->addDatabaseLine->handle(Line::fromRaw('foo.first', ['nl' => 'nl value', 'en' => 'en value']));
        $this->addDatabaseLine->handle(Line::fromRaw('foo.second', ['nl' => 'nl value', 'en' => 'en value']));
        $this->addDatabaseLine->handle(Line::fromRaw('foo.third', ['nl' => 'nl value', 'en' => 'en value']));

        $completion = Completion::fromDatabaseLines(DatabaseLine::all());

        $this->assertTrue($completion->isCompleteForAllLocales());
    }

    /** @test */
    public function it_can_return_the_completion_percentage_for_a_locale()
    {
        $this->addDatabaseLine->handle(Line::fromRaw('foo.first', ['nl' => 'nl value', 'en' => 'en value']));
        $this->addDatabaseLine->handle(Line::fromRaw('foo.second', ['nl' => 'nl value', 'en' => null]));
        $this->addDatabaseLine->handle(Line::fromRaw('foo.third', ['nl' => 'nl value', 'en' => 'en value']));

        $completion = Completion::fromDatabaseLines(DatabaseLine::all());

        $this->assertEquals(1.0, $completion->asPercentage('nl'));
        $this->assertEquals(0.67, $completion->asPercentage('en'));
    }
}
