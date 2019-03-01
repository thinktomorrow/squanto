<?php

namespace Thinktomorrow\Squanto\Tests;

use Thinktomorrow\Squanto\Domain\PageKey;
use Thinktomorrow\Squanto\Exceptions\InvalidPageKeyException;

class PageKeyTest extends TestCase
{
    /** @test */
    public function block_unexpected_key_format()
    {
        $this->expectException(InvalidPageKeyException::class);

        new PageKey('foo.title');
    }

    /** @test */
    public function block_non_string_key()
    {
        $this->expectException(InvalidPageKeyException::class);

        new PageKey(12);
    }

    /**
     * Main usage of the squanto
     * @test
     */
    public function allow_expected_key_format()
    {
        $pageid = new PageKey('foo');
        $this->assertEquals('foo', $pageid->get());
    }

    /** @test */
    public function it_suggests_a_humanreadable_label()
    {
        // Page key is always removed for the label
        $pageid = new PageKey('foo');
        $this->assertEquals('Foo', $pageid->getAsLabel());
    }

    public function it_can_create_the_page_key_from_linekey()
    {
        $pageid = PageKey::fromLineKeyString('foo.bar');
        $this->assertEquals('foo', $pageid->get());
    }

    /** @test */
    public function it_can_check_if_linekey_comes_from_excluded_source()
    {
        PageKey::refreshExcludedSources();

        $existing = config()->get('squanto.excluded_files');
        config()->set('squanto.excluded_files', ['foo']);

        $this->assertTrue(PageKey::fromString('foo')->isExcludedSource());
        $this->assertFalse(PageKey::fromString('foobar')->isExcludedSource());

        config()->set('squanto.excluded_files', $existing);

        // Reset the static Pagekey property of the excluded files
        $lineKey = PageKey::fromString('foo');
        $reflection = new \ReflectionObject($lineKey);
        $excluded_reflection = $reflection->getProperty('excludedSources');
        $excluded_reflection->setAccessible(true);
        $excluded_reflection->setValue($existing);
    }
}
