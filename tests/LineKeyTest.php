<?php

namespace Thinktomorrow\Squanto\Tests;

use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\Squanto\Domain\DomainException;

class LineKeyTest extends TestCase
{
    /** @test */
    public function block_unexpected_key_format()
    {
        $this->expectException(DomainException::class);
        new LineKey('foo');
    }

    /** @test */
    public function block_non_string_key()
    {
        $this->expectException(DomainException::class);
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
}
