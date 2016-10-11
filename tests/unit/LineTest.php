<?php

namespace Thinktomorrow\Squanto\Tests;

use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Domain\Page;

class LineTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_store_a_translation()
    {
        $line = Line::make('foo.bar');
        $line->saveValue('nl','bazz');

        $this->assertEquals('bazz',$line->value);
    }

    /** @test */
    public function it_can_retrieve_a_translation()
    {
        Line::make('foo.bar')->saveValue('nl','bazz');

        $this->assertInstanceOf(Line::class,Line::findByKey('foo.bar'));
        $this->assertEquals('bazz',Line::findByKey('foo.bar')->value);
    }

    /** @test */
    public function on_creation_it_creates_a_page_based_on_the_key()
    {
        $line = Line::make('funky.fungi');
        $page = Page::findByKey('funky');

        $this->assertInstanceOf(Page::class,$page);
        $this->assertEquals($line->page_id,$page->id);
    }

    /** @test */
    public function on_creation_it_attaches_an_existing_page()
    {
        $page = Page::make('bozo');
        $line = Line::make('bozo.clown');

        $this->assertInstanceOf(Page::class,$page);
        $this->assertEquals($line->page_id,$page->id);
    }

}
