<?php

namespace Thinktomorrow\Squanto\Tests;

use Thinktomorrow\Squanto\Domain\DatabaseLine;
use Thinktomorrow\Squanto\Domain\Page;

class PageTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    /** @test */
    public function it_can_get_completion_stats_on_page_object()
    {
        $line = DatabaseLine::createFromKey('foo.bar');
        $page = Page::findByKey('foo');
        $line->saveValue('nl', 'bazz');
        $line->saveValue('en', 'bazz');
        $line->saveValue('fr', 'bazz');
        $this->assertTrue($page->isCompleted());

        $line = DatabaseLine::createFromKey('fooo.vaz');
        $page = Page::findByKey('fooo');
        $line->saveValue('fr', 'bazz');
        $line = DatabaseLine::createFromKey('fooo.va');
        $line->saveValue('fr', 'bazz');
        $line = DatabaseLine::createFromKey('fooo.baz');
        $line->saveValue('fr', 'bazz');
        $line = DatabaseLine::createFromKey('fooo.ba');
        $line->saveValue('fr', 'bazz');

        $this->assertFalse($page->isCompleted());
    }

    /** @test */
    public function it_can_get_completion_stats_per_page_per_locale_on_page_object()
    {
        $line = DatabaseLine::createFromKey('foo.bar');
        $page = Page::findByKey('foo');
        $line->saveValue('nl', 'bazz');
        $line->saveValue('en', 'bazz');
        $line->saveValue('fr', 'bazz');
        $this->assertEquals(100.0, $page->completionPercentage('en'));

        $line = DatabaseLine::createFromKey('fooo.vaz');
        $page = Page::findByKey('fooo');
        $line->saveValue('fr', 'bazz');
        DatabaseLine::createFromKey('fooo.va');
        DatabaseLine::createFromKey('fooo.baz');
        DatabaseLine::createFromKey('fooo.ba');

        $this->assertEquals(25.0, $page->completionPercentage('fr'));
    }

    /** @test */
    public function it_returns_true_if_there_are_no_lines_on_page_object()
    {
        $page = Page::createFromKey('foo');
        $this->assertTrue($page->isCompleted());
    }

    /** @test */
    public function it_returns_100_if_there_are_no_lines_on_page_object()
    {
        $page = Page::createFromKey('foo');
        $this->assertEquals(100, $page->completionPercentage('fr'));
    }
}
