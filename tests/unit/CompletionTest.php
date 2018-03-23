<?php

namespace Thinktomorrow\Squanto\Tests;

use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Domain\Page;
use Thinktomorrow\Squanto\Domain\Completion;

class CompletionTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_get_completion_stats_per_page()
    {
        $line = Line::make('foo.bar');
        $page = Page::findByKey('foo');
        $line->saveValue('nl', 'bazz');
        $line->saveValue('en', 'bazz');
        $line->saveValue('fr', 'bazz');
        $this->assertTrue(Completion::getPageCompletion($page->id));

        $line = Line::make('foo.vaz');
        $page = Page::findByKey('foo');
        $line->saveValue('fr', 'bazz');
        Line::make('foo.va');
        Line::make('foo.baz');
        Line::make('foo.ba');

        $this->assertFalse(Completion::getPageCompletion($page->id));
    }

    /** @test */
    public function it_can_get_completion_stats_per_page_per_locale()
    {
        $line = Line::make('foo.bar');
        $page = Page::findByKey('foo');
        $line->saveValue('nl', 'bazz');
        $line->saveValue('en', 'bazz');
        $line->saveValue('fr', 'bazz');
        $this->assertEquals(100, Completion::getPageCompletionPerLocale($page->id, 'en'));

        $line = Line::make('foo.vaz');
        $page = Page::findByKey('foo');
        $line->saveValue('fr', 'bazz');
        Line::make('foo.va');
        Line::make('foo.baz');
        Line::make('foo.ba');

        $this->assertFalse(25, Completion::getPageCompletionPerLocale($page->id, 'fr'));
    }
}
