<?php

namespace Thinktomorrow\Squanto\Domain;

use Thinktomorrow\Squanto\Domain\Exceptions\InvalidLineValue;

final class LineValue
{
    private array $values;

    private function __construct(array $values)
    {
        $this->validateValues($values);

        $this->values = $values;
    }

    public static function fromArray(array $values): self
    {
        return new static($values);
    }

    public function value(string $locale): ?string
    {
        if(!array_key_exists($locale, $this->values)) {
            return null;
        }

        return $this->values[$locale];
    }

    public function all(): array
    {
        return $this->values;
    }

    public function merge(LineValue $otherLineValue): self
    {
        return new static(array_merge($this->values, $otherLineValue->values));
    }

    /**
     * Values should consist of locale:value pairs.
     *
     * @param array $values
     * @throws InvalidLineValue
     */
    private function validateValues(array $values): void
    {
        foreach ($values as $locale => $value) {
            if (!is_string($locale)) {
                throw new InvalidLineValue('LineValue should contain locale:value pairs. Invalid locale [' . $locale . '] given.');
            }

            if (!is_string($value) && null !== $value) {
                throw new InvalidLineValue('A value is expected to be of type string or null. A value of type ' . gettype($value) . ' is passed instead.');
            }
        }
    }
}
