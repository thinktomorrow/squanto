<?php

namespace Thinktomorrow\Squanto\Tests;

use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\Squanto\Exceptions\InvalidLineKeyException;

class LineKeyTest extends TestCase
{
    /** @test */
    public function block_unexpected_key_format()
    {
        $this->setExpectedException(InvalidLineKeyException::class);

        new LineKey('foo');
    }

    /** @test */
    public function block_non_string_key()
    {
        $this->setExpectedException(InvalidLineKeyException::class);

        new LineKey(12.04);
    }

    /**
     * Main usage of the squanto
     * @test
     */
    public function allow_expected_key_format()
    {
        $lineid = new LineKey('foo.bar');
        $this->assertEquals('foo.bar',$lineid->get());

        $lineid = new LineKey('Foo.BAR');
        $this->assertEquals('foo.bar',$lineid->get());
    }

    /** @test */
    public function it_suggests_a_humanreadable_label()
    {
        // Page key is always removed for the label
        $lineid = new LineKey('foo.bar.zooka');
        $this->assertEquals('Bar zooka',$lineid->getAsLabel());
    }

    /** @test */
    public function it_can_extract_the_page_key()
    {
        $lineid = new LineKey('foo.bar');
        $this->assertEquals('foo',$lineid->getPageKey());
    }

    /** @test */
    public function it_can_check_if_linekey_comes_from_excluded_source()
    {
        $existing = config()->get('squanto.excluded_files');
        config()->set('squanto.excluded_files',['foo']);

        $this->assertTrue(LineKey::fromString('foo.bar')->isExcludedSource());
        $this->assertFalse(LineKey::fromString('foobar.test')->isExcludedSource());

        config()->set('squanto.excluded_files',$existing);

        // Reset the static Linekey property of the excluded files
        $lineKey = new LineKey('foo.bar');
        $reflection = new \ReflectionObject($lineKey);
        $excluded_reflection = $reflection->getProperty('excludedSources');
        $excluded_reflection->setAccessible(true);
        $excluded_reflection->setValue($existing);
    }
}
