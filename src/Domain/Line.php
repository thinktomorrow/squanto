<?php
declare(strict_types=1);

namespace Thinktomorrow\Squanto\Domain;

final class Line
{
    /**
     * @var LineKey 
     */
    private LineKey $lineKey;

    /**
     * @var LineValue 
     */
    private LineValue $lineValue;

    public function __construct(LineKey $lineKey, LineValue $lineValue)
    {
        $this->lineKey = $lineKey;
        $this->lineValue = $lineValue;
    }

    public static function fromRaw(string $key, array $value): self
    {
        return new static(LineKey::fromString($key), LineValue::fromArray($value));
    }

    public function keyAsString(): string
    {
        return $this->lineKey->get();
    }

    public function value(string $locale): ?string
    {
        return $this->lineValue->value($locale);
    }

    public function values(): array
    {
        return $this->lineValue->all();
    }

    public function merge(Line $otherLine): self
    {
        return new static($this->lineKey, $this->lineValue->merge($otherLine->lineValue));
    }
}
