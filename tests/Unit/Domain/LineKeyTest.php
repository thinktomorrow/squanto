<?php

declare(strict_types=1);

namespace Thinktomorrow\SquantoTests\Unit\Domain;

use Thinktomorrow\Squanto\Domain\Exceptions\InvalidLineKeyException;
use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\SquantoTests\TestCase;

class LineKeyTest extends TestCase
{
    /** @test */
    public function block_unexpected_key_format()
    {
        $this->expectException(InvalidLineKeyException::class);

        LineKey::fromString('foo');
    }

    /** @test */
    public function block_non_string_key()
    {
        // This test only works if strict types are declared
        $this->expectException(\TypeError::class);

        LineKey::fromString(12.04);
    }

    /**
     * Main usage of the squanto
     * @test
     */
    public function allow_expected_key_format()
    {
        $lineid = LineKey::fromString('foo.bar');
        $this->assertEquals('foo.bar', $lineid->get());

        $lineid = LineKey::fromString('Foo.BAR');
        $this->assertEquals('foo.bar', $lineid->get());
    }

    /** @test */
    public function it_can_extract_the_page_key()
    {
        $lineid = LineKey::fromString('foo.bar');
        $this->assertEquals('foo', $lineid->pageKey());
    }
}
