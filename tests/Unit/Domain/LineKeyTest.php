<?php

declare(strict_types=1);

namespace Thinktomorrow\SquantoTests\Unit\Domain;

use Thinktomorrow\Squanto\Domain\Exceptions\InvalidLineKeyException;
use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\SquantoTests\TestCase;

class LineKeyTest extends TestCase
{
    public function test_block_unexpected_key_format()
    {
        $this->expectException(InvalidLineKeyException::class);

        LineKey::fromString('foo');
    }

    public function test_block_non_string_key()
    {
        // This test only works if strict types are declared
        $this->expectException(\TypeError::class);

        LineKey::fromString(12.04);
    }

    /**
     * Main usage of the squanto
     */
    public function test_allow_expected_key_format()
    {
        $lineid = LineKey::fromString('foo.bar');
        $this->assertEquals('foo.bar', $lineid->get());

        $lineid = LineKey::fromString('Foo.BAR');
        $this->assertEquals('foo.bar', $lineid->get());

        $lineid = LineKey::fromString('Foo.bar_baz');
        $this->assertEquals('foo.bar_baz', $lineid->get());
    }

    public function test_it_can_extract_the_page_key()
    {
        $lineid = LineKey::fromString('foo.bar');
        $this->assertEquals('foo', $lineid->pageKey());
    }
}
