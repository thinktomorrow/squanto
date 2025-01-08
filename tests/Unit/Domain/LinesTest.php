<?php
declare(strict_types=1);

namespace Thinktomorrow\SquantoTests\Unit\Domain;

use Thinktomorrow\Squanto\Domain\Exceptions\InvalidLineKeyException;
use Thinktomorrow\Squanto\Domain\Exceptions\InvalidLineValue;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Domain\Lines;
use Thinktomorrow\SquantoTests\TestCase;

final class LinesTest extends TestCase
{
    public function test_it_can_get_a_single_line()
    {
        $lines = new Lines([
            Line::fromRaw('page.foo', ['nl' => 'bar']),
        ]);

        $this->assertInstanceOf(Line::class, $lines->find('page.foo'));
    }

    public function test_it_can_get_all_lines_for_a_specific_locale()
    {
        $lines = new Lines([
            Line::fromRaw('page.foo', ['nl' => 'bar']),
            Line::fromRaw('page.faa', ['nl' => 'baz']),
        ]);

        $this->assertEquals([
            'page.foo' => 'bar',
            'page.faa' => 'baz',
        ], $lines->values('nl'));
    }

    public function test_it_returns_null_for_a_non_found_line()
    {
        $lines = new Lines([
            Line::fromRaw('page.foo', ['nl' => 'bar']),
        ]);

        $this->assertNull($lines->find('xxx'));
        $this->assertNull($lines->find('page.xxx'));
    }

    public function test_it_returns_a_null_value_for_non_found_locale_value()
    {
        $lines = new Lines([
            Line::fromRaw('page.foo', ['en' => 'bar']),
        ]);

        $this->assertEquals(['page.foo' => null], $lines->values('nl'));
        $this->assertEquals(['page.foo' => 'bar'], $lines->values('en'));
    }

    public function test_it_can_merge_new_lines_and_overwrites_existing_values()
    {
        $lines = new Lines([
            Line::fromRaw('page.foo', ['nl' => 'bar']),
            Line::fromRaw('page.faa', ['nl' => 'baz']),
        ]);

        $this->assertEquals([
            'page.foo' => 'bar',
            'page.faa' => 'BAM',
            'page.boz' => 'biz',
        ], $lines->merge(new Lines([
            Line::fromRaw('page.faa', ['nl' => 'BAM']),
            Line::fromRaw('page.boz', ['nl' => 'biz']),
        ]))->values('nl'));

        // Assert immutability of original object
        $this->assertEquals([
            'page.foo' => 'bar',
            'page.faa' => 'baz',
        ], $lines->values('nl'));
    }

    public function test_it_can_merge_new_lines_and_add_new_locale_values()
    {
        $lines = new Lines([
            Line::fromRaw('page.foo', ['nl' => 'bar']),
            Line::fromRaw('page.faa', ['nl' => 'baz']),
        ]);

        $this->assertEquals([
            'page.foo' => 'bar',
            'page.faa' => 'baz',
        ], $lines->merge(new Lines([
            Line::fromRaw('page.faa', ['en' => 'bobby']),
        ]))->values('nl'));

        $this->assertEquals([
            'page.foo' => null,
            'page.faa' => 'bobby',
        ], $lines->merge(new Lines([
            Line::fromRaw('page.faa', ['en' => 'bobby']),
        ]))->values('en'));
    }

    public function test_it_does_not_accept_nested_arrays()
    {
        $this->expectException(InvalidLineValue::class);

        new Lines([
            Line::fromRaw('page.foo', ['nl' => ['bor' => 'baz']]),
        ]);
    }

    public function test_it_does_not_accept_a_key_without_dot_separator()
    {
        $this->expectException(InvalidLineKeyException::class);

        new Lines([
            Line::fromRaw('foo', ['nl' => 'bar']),
        ]);
    }

    public function test_it_does_not_accept_a_integer_typed_key()
    {
        $this->expectException(InvalidLineValue::class);

        new Lines([Line::fromRaw('page.foo', ['bar'])]);
    }
}
