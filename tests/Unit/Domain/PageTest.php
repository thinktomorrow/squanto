<?php

declare(strict_types=1);

namespace Thinktomorrow\SquantoTests\Unit\Domain;

use Thinktomorrow\SquantoTests\TestCase;
use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\Squanto\Manager\Pages\Page;

class PageTest extends TestCase
{
    /** @test */
    public function page_from_lineKey_matches_original_filename()
    {
        $lineid = LineKey::fromString('foo_bar.baz');
        $this->assertEquals('foo_bar', $lineid->pageKey());

        $lineid = LineKey::fromString('foo-bar.baz');
        $this->assertEquals('foo-bar', $lineid->pageKey());
    }

    /** @test */
    public function page_slug_must_match_linekey_first_part()
    {
        $page = Page::fromFilename('foo_bar');

        $this->assertEquals('foo bar', $page->label());
        $this->assertEquals('foo_bar', $page->slug());

        $page = Page::fromFilename('foo-bar');

        $this->assertEquals('foo bar', $page->label());
        $this->assertEquals('foo-bar', $page->slug());
    }
}
