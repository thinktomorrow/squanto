<?php

namespace Thinktomorrow\SquantoTests\Domain;

use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\SquantoTests\TestCase;
use Thinktomorrow\Squanto\Domain\Exceptions\InvalidLineValue;

class LineTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated_from_raw_values()
    {
        $line = Line::fromRaw('page.foo', ['nl' => 'valid value']);

        $this->assertInstanceOf(Line::class, $line);
    }

    /** @test */
    public function it_cannot_be_instantiated_with_non_associative_array_as_value()
    {
        $this->expectException(InvalidLineValue::class);

        Line::fromRaw('page.foo', ['invalid value']);
    }

    /** @test */
    public function it_can_provide_the_key_as_string()
    {
        $line = Line::fromRaw('page.foo', ['nl' => 'valid value']);

        $this->assertEquals('page.foo', $line->keyAsString());
    }

    /** @test */
    public function it_can_merge_line_values()
    {
        $line = Line::fromRaw('page.foo', ['nl' => 'value nl']);
        $otherLine = Line::fromRaw('page.foo', ['en' => 'value en']);

        $mergedLine = $line->merge($otherLine);

        $this->assertEquals('value nl', $mergedLine->value('nl'));
        $this->assertEquals('value en', $mergedLine->value('en'));
    }
}
