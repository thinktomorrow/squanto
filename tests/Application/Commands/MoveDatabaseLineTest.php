<?php

namespace Thinktomorrow\Squanto\Tests\Application\Commands;

use Thinktomorrow\Squanto\Application\Commands\MoveDatabaseLine;
use Thinktomorrow\Squanto\Domain\DatabaseLine;
use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\Squanto\Tests\TestCase;

class MoveDatabaseLineTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    /** @test */
    public function it_can_rename_a_line_key()
    {
        $this->addDatabaseLine('first-page.first-value', []);

        $this->assertEquals('first-page.first-value', $this->databaseLine->key);

        app(MoveDatabaseLine::class)->handle(LineKey::fromString('first-page.first-value'), LineKey::fromString('first-page.new-value'));
        $movedDatabaseLine = DatabaseLine::find($this->databaseLine->id);

        $this->assertEquals('first-page.new-value', $movedDatabaseLine->key);
        $this->assertEquals($this->databaseLine->page_id, $movedDatabaseLine->page_id);
    }

    /** @test */
    public function it_will_not_rename_a_line_key_that_does_not_exist()
    {
        $this->addDatabaseLine('first-page.first-value', []);

        $this->assertEquals('first-page.first-value', $this->databaseLine->key);

        app(MoveDatabaseLine::class)->handle(LineKey::fromString('first-page.non-existing-value'), LineKey::fromString('first-page.new-value'));

        $movedDatabaseLine = DatabaseLine::find($this->databaseLine->id);

        $this->assertEquals('first-page.first-value', $movedDatabaseLine->key);
        $this->assertEquals($this->databaseLine->page_id, $movedDatabaseLine->page_id);
    }

    /** @test */
    public function it_can_move_a_line_to_a_different_page()
    {
        $this->addDatabaseLine('first-page.first-value', []);

        $this->assertEquals('first-page.first-value', $this->databaseLine->key);

        app(MoveDatabaseLine::class)->handle(LineKey::fromString('first-page.first-value'), LineKey::fromString('second-page.new-value'));
        $movedDatabaseLine = DatabaseLine::find($this->databaseLine->id);

        $this->assertEquals('second-page.new-value', $movedDatabaseLine->key);
        $this->assertNotEquals($this->databaseLine->page_id, $movedDatabaseLine->page_id);
    }
}