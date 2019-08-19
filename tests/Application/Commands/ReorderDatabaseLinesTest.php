<?php

namespace Thinktomorrow\Squanto\Tests\Application\Commands;

use Thinktomorrow\Squanto\Application\Commands\ReorderDatabaseLines;
use Thinktomorrow\Squanto\Domain\DatabaseLine;
use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\Squanto\Domain\PageKey;
use Thinktomorrow\Squanto\Tests\TestCase;

class ReorderDatabaseLinesTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    /** @test */
    public function it_can_reorder_existing_lines()
    {
        $this->addDatabaseLine('first-page.first-value', []);
        $this->addDatabaseLine('first-page.second-value', []);

        app(ReorderDatabaseLines::class)->handle(PageKey::fromString('first-page'), [
            'first-page.second-value',
            'first-page.first-value',
        ]);

        $firstDatabaseLine = DatabaseLine::findByKey(LineKey::fromString('first-page.first-value'));
        $secondDatabaseLine = DatabaseLine::findByKey(LineKey::fromString('first-page.second-value'));

        $this->assertEquals(0, $secondDatabaseLine->sequence);
        $this->assertEquals(1, $firstDatabaseLine->sequence);
    }
}