<?php

declare(strict_types=1);

namespace Thinktomorrow\Squanto\Domain\Metadata;

use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Domain\LineKey;

final class Metadata
{
    private LineKey $lineKey;

    private array $values;

    public function __construct(LineKey $lineKey, array $values)
    {
        $this->lineKey = $lineKey;
        $this->values = $values;
    }

    public static function fromLine(Line $line): self
    {
        return new self(
            LineKey::fromString($line->keyAsString()), [
                'label' => null,
                'description' => null,
                'fieldtype' => self::guessFieldType($line),
            ]);
    }

    public static function fromRaw(string $key, array $values): self
    {
        return new self(LineKey::fromString($key), $values);
    }

    public function values(): array
    {
        return $this->values;
    }

    public function keyAsString(): string
    {
        return $this->lineKey->get();
    }

    private static function guessFieldType(Line $line): string
    {
        $values = $line->values();

        // We take the longest translation value as a reference to guess the proper fieldtype.
        uasort(
            $values,
            function ($a, $b) {
                $comp = strlen($a ?? '') < strlen($b ?? '');

                return $comp ? 1 : -1;
            }
        );

        $valueForGuessingFieldType = reset($values);

        return FieldType::guess($valueForGuessingFieldType)->get();
    }

    public function merge(Metadata $other): self
    {
        return new self($this->lineKey, array_merge($this->values, $other->values));
    }
}
