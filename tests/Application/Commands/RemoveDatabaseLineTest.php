<?php

namespace Thinktomorrow\Squanto\Tests\Application\Commands;

use Thinktomorrow\Squanto\Application\Commands\RemoveDatabaseLine;
use Thinktomorrow\Squanto\Domain\DatabaseLine;
use Thinktomorrow\Squanto\Domain\DatabaseLineTranslation;
use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\Squanto\Tests\TestCase;

class RemoveDatabaseLineTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();

        $this->addDatabaseLine('first-page.first-value', [
            'nl' => 'new-first-value-nl',
            'fr' => 'new-first-value-fr',
        ]);
    }

    /** @test */
    public function it_can_remove_a_line_to_database()
    {
        app(RemoveDatabaseLine::class)->handle(LineKey::fromString($this->databaseLine->key));

        $this->assertCount(0, DatabaseLine::all());
        $this->assertCount(0, DatabaseLineTranslation::all());
    }
}