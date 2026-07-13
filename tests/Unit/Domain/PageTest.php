<?php

declare(strict_types=1);

namespace Thinktomorrow\SquantoTests\Unit\Domain;

use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\Squanto\Manager\Pages\Page;
use Thinktomorrow\Squanto\Sources\SquantoSource;
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

    public function test_page_can_be_created_from_a_namespaced_source_file(): void
    {
        $page = Page::fromSourceFile(
            SquantoSource::make(__DIR__.'/../../stubs/plugin/lang', 'chief-form-plugin', 'Form plugin'),
            'general'
        );

        $this->assertEquals('general', $page->label());
        $this->assertEquals('chief-form-plugin::general', $page->slug());
        $this->assertEquals('chief-form-plugin::general', $page->pageKey());
        $this->assertEquals('chief-form-plugin', $page->namespace());
        $this->assertEquals('general', $page->group());
        $this->assertEquals('Form plugin', $page->sourceLabel());
    }
}
