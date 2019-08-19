<?php

namespace Thinktomorrow\Squanto\Tests;

use Thinktomorrow\Squanto\Domain\DatabaseLine;
use Thinktomorrow\Squanto\Domain\Page;

class LineTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    /** @test */
    public function it_can_store_a_translation()
    {
        $line = DatabaseLine::createFromKey('foo.bar');
        $line->saveValue('nl', 'bazz');

        $this->assertEquals('bazz', $line->value);
    }

    /** @test */
    public function it_can_retrieve_a_translation()
    {
        DatabaseLine::createFromKey('foo.bar')->saveValue('nl', 'bazz');

        $this->assertInstanceOf(DatabaseLine::class, DatabaseLine::findByKey('foo.bar'));
        $this->assertEquals('bazz', DatabaseLine::findByKey('foo.bar')->value);
    }

    /** @test */
    public function on_creation_it_creates_a_page_based_on_the_key()
    {
        $line = DatabaseLine::createFromKey('funky.fungi');
        $page = Page::findByKey('funky');

        $this->assertInstanceOf(Page::class, $page);
        $this->assertEquals($line->page_id, $page->id);
    }

    /** @test */
    public function on_creation_it_attaches_an_existing_page()
    {
        $page = Page::createFromKey('bozo');
        $line = DatabaseLine::createFromKey('bozo.clown');

        $this->assertInstanceOf(Page::class, $page);
        $this->assertEquals($line->page_id, $page->id);
    }

    /** @test */
    public function list_all_lines_per_locale()
    {
        DatabaseLine::createFromKey('bozo.clown')
            ->saveValue('nl', 'value-nl')
            ->saveValue('fr', 'value-fr');
        DatabaseLine::createFromKey('bozo.clown2')
            ->saveValue('nl', 'value-nl-2');

        $lines = DatabaseLine::getValuesByLocale('nl');

        $this->assertCount(2, $lines);
        $this->assertInternalType('array', $lines);

        $lines = DatabaseLine::getValuesByLocale('fr');
        $this->assertCount(1, $lines);
    }
}
