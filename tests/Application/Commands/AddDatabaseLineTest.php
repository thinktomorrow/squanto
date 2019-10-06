<?php

namespace Thinktomorrow\Squanto\Tests\Application\Commands;

use Thinktomorrow\Squanto\Application\Commands\AddDatabaseLine;
use Thinktomorrow\Squanto\Domain\DatabaseLine;
use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\Squanto\Tests\TestCase;

class AddDatabaseLineTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    /** @test */
    public function it_can_add_a_new_line_to_database()
    {
        $lineKey = LineKey::fromString('first-page.first-value');

        app(AddDatabaseLine::class)->handle($lineKey, [
            'nl' => 'new-first-value-nl',
            'fr' => 'new-first-value-fr',
        ]);

        $databaseLine = DatabaseLine::first();

        $this->assertCount(2, $databaseLine->translations()->get());
        $this->assertEquals('first-page.first-value', $databaseLine->key);

        app()->setLocale('nl');
        $this->assertEquals('new-first-value-nl', $databaseLine->value);

        app()->setLocale('fr');
        $this->assertEquals('new-first-value-fr', $databaseLine->value);
    }

    /** @test */
    public function it_can_add_an_empty_new_line()
    {
        $lineKey = LineKey::fromString('first-page.first-value');

        app(AddDatabaseLine::class)->handle($lineKey, []);

        $databaseLine = DatabaseLine::first();

        $this->assertCount(0, $databaseLine->translations()->get());
        $this->assertEquals('first-page.first-value', $databaseLine->key);
    }

    /** @test */
    public function it_will_not_replace_existing_database_values()
    {
        $lineKey = LineKey::fromString('first-page.first-value');

        app(AddDatabaseLine::class)->handle($lineKey, [
            'nl' => 'new-first-value-nl',
            'fr' => 'new-first-value-fr',
        ]);

        app(AddDatabaseLine::class)->handle($lineKey, [
            'nl' => 'unwanted-first-value-nl',
            'fr' => 'unwanted-first-value-fr',
        ]);

        $databaseLine = DatabaseLine::first();

        $this->assertCount(1, DatabaseLine::all());
        $this->assertCount(2, $databaseLine->translations()->get());
        $this->assertEquals('first-page.first-value', $databaseLine->key);

        app()->setLocale('nl');
        $this->assertEquals('new-first-value-nl', $databaseLine->value);

        app()->setLocale('fr');
        $this->assertEquals('new-first-value-fr', $databaseLine->value);
    }

    /** @test */
    public function it_can_force_replace_existing_database_values()
    {
        $lineKey = LineKey::fromString('first-page.first-value');

        app(AddDatabaseLine::class)->handle($lineKey, [
            'nl' => 'new-first-value-nl',
            'fr' => 'new-first-value-fr',
        ]);

        app(AddDatabaseLine::class)->forceReplacement()->handle($lineKey, [
            'nl' => 'wanted-first-value-nl',
            'fr' => 'wanted-first-value-fr',
        ]);

        $databaseLine = DatabaseLine::first();

        $this->assertCount(1, DatabaseLine::all());
        $this->assertCount(2, $databaseLine->translations()->get());
        $this->assertEquals('first-page.first-value', $databaseLine->key);

        app()->setLocale('nl');
        $this->assertEquals('wanted-first-value-nl', $databaseLine->value);

        app()->setLocale('fr');
        $this->assertEquals('wanted-first-value-fr', $databaseLine->value);
    }
}
