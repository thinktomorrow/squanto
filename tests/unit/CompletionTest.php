<?php

namespace Thinktomorrow\Squanto\Tests;

use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Domain\Page;
use Thinktomorrow\Squanto\Domain\Completion;

class CompletionTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    /** @test */
    public function it_can_get_completion_stats_per_page()
    {
        $line = Line::make('foo.bar');
        $page = Page::findByKey('foo');
        $line->saveValue('nl', 'bazz');
        $line->saveValue('en', 'bazz');
        $line->saveValue('fr', 'bazz');
        $this->assertTrue(Completion::check($page));

        $line = Line::make('fooo.vaz');
        $page = Page::findByKey('fooo');
        $line->saveValue('fr', 'bazz');
        $line = Line::make('fooo.va');
        $line->saveValue('fr', 'bazz');
        $line = Line::make('fooo.baz');
        $line->saveValue('fr', 'bazz');
        $line = Line::make('fooo.ba');
        $line->saveValue('fr', 'bazz');

        $this->assertFalse(Completion::check($page));
    }

    /** @test */
    public function it_can_get_completion_stats_per_page_per_locale()
    {
        $line = Line::make('foo.bar');
        $page = Page::findByKey('foo');
        $line->saveValue('nl', 'bazz');
        $line->saveValue('en', 'bazz');
        $line->saveValue('fr', 'bazz');
        $this->assertEquals(100.0, Completion::asPercentage($page, 'en'));

        $line = Line::make('fooo.vaz');
        $page = Page::findByKey('fooo');
        $line->saveValue('fr', 'bazz');
        Line::make('fooo.va');
        Line::make('fooo.baz');
        Line::make('fooo.ba');

        $this->assertEquals(25.0, Completion::asPercentage($page, 'fr'));
    }

    /** @test */
    public function it_returns_true_if_there_are_no_lines()
    {
        $page = Page::make('foo');
        $this->assertTrue(Completion::check($page));
    }

    /** @test */
    public function it_returns_100_if_there_are_no_lines()
    {
        $page = Page::make('foo');
        $this->assertEquals(100, Completion::asPercentage($page, 'fr'));
    }
}
