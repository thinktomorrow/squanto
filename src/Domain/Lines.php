<?php
declare(strict_types=1);

namespace Thinktomorrow\Squanto\Domain;

use Thinktomorrow\Squanto\Services\KeyedCollection;
use Thinktomorrow\Squanto\Domain\Exceptions\InvalidLinesArray;

/**
 * Class Lines
 *
 * @method null|Line find(string $key)
 * @method bool exists(string $key)
 */
final class Lines
{
    use KeyedCollection;

    private array $items;

    public function __construct(array $lines)
    {
        $this->validateLines($lines);

        $this->items = static::keyify($lines);
    }

    public static function fromArray(string $locale, array $lines): self
    {
        $preppedLines = [];

        foreach ($lines as $key => $value) {
            // Empty arrays are not prepped
            if ($value === []) {
                continue;
            }
            $preppedLines[] = Line::fromRaw($key, [$locale => $value]);
        }

        return new static($preppedLines);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function merge(Lines $lines): self
    {
        $mergedLines = $this->mergeByKey($this->items, $lines->items);

        return new static($mergedLines);
    }

    public function each(callable $callback)
    {
        foreach ($this->items as $line) {
            $callback($line);
        }
    }

    /**
     * Return all the values for a specific locale in key:value pairs
     *
     * @param string $locale
     * @return array
     */
    public function values(string $locale): array
    {
        $result = [];

        /** @var Line $line */
        foreach ($this->items as $line) {
            $result[$line->keyAsString()] = $line->value($locale);
        }

        return $result;
    }

    private function validateLines(array $lines): void
    {
        foreach ($lines as $line) {
            if (!$line instanceof Line) {
                throw new InvalidLinesArray('A lines parameter should consist of Line instances.');
            }
        }
    }
}
