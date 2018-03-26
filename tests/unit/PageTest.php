<?php

namespace Thinktomorrow\Squanto\Tests;

use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Domain\Page;
use Thinktomorrow\Squanto\Domain\Completion;

class PageTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_get_completion_stats_on_page_object()
    {
        $line = Line::make('foo.bar');
        $page = Page::findByKey('foo');
        $line->saveValue('nl', 'bazz');
        $line->saveValue('en', 'bazz');
        $line->saveValue('fr', 'bazz');
        $this->assertTrue($page->isCompleted());

        $line = Line::make('fooo.vaz');
        $page = Page::findByKey('fooo');
        $line->saveValue('fr', 'bazz');
        $line = Line::make('fooo.va');
        $line->saveValue('fr', 'bazz');
        $line = Line::make('fooo.baz');
        $line->saveValue('fr', 'bazz');
        $line = Line::make('fooo.ba');
        $line->saveValue('fr', 'bazz');

        $this->assertFalse($page->isCompleted());
    }

    /** @test */
    public function it_can_get_completion_stats_per_page_per_locale_on_page_object()
    {
        $line = Line::make('foo.bar');
        $page = Page::findByKey('foo');
        $line->saveValue('nl', 'bazz');
        $line->saveValue('en', 'bazz');
        $line->saveValue('fr', 'bazz');
        $this->assertEquals(100.0, $page->completionPercentage('en'));

        $line = Line::make('fooo.vaz');
        $page = Page::findByKey('fooo');
        $line->saveValue('fr', 'bazz');
        Line::make('fooo.va');
        Line::make('fooo.baz');
        Line::make('fooo.ba');

        $this->assertEquals(25.0, $page->completionPercentage('fr'));
    }

    /** @test */
    public function it_returns_true_if_there_are_no_lines_on_page_object()
    {
        $page = Page::make('foo');
        $this->assertTrue($page->isCompleted());
    }

    /** @test */
    public function it_returns_100_if_there_are_no_lines_on_page_object()
    {
        $page = Page::make('foo');
        $this->assertEquals(100, $page->completionPercentage('fr'));
    }
}
