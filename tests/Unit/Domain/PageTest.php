<?php

declare(strict_types=1);

namespace Thinktomorrow\SquantoTests\Unit\Domain;

use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\Squanto\Manager\Pages\Page;
use Thinktomorrow\SquantoTests\TestCase;

class PageTest extends TestCase
{
    public function test_page_from_line_key_matches_original_filename()
    {
        $lineid = LineKey::fromString('foo_bar.baz');
        $this->assertEquals('foo_bar', $lineid->pageKey());

        $lineid = LineKey::fromString('foo-bar.baz');
        $this->assertEquals('foo-bar', $lineid->pageKey());
    }

    public function test_page_slug_must_match_linekey_first_part()
    {
        $page = Page::fromFilename('foo_bar');

        $this->assertEquals('foo bar', $page->label());
        $this->assertEquals('foo_bar', $page->slug());

        $page = Page::fromFilename('foo-bar');

        $this->assertEquals('foo bar', $page->label());
        $this->assertEquals('foo-bar', $page->slug());
    }
}
