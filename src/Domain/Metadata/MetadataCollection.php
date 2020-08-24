<?php
declare(strict_types=1);

namespace Thinktomorrow\Squanto\Domain\Metadata;

use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Domain\Lines;
use Thinktomorrow\Squanto\Services\KeyedCollection;
use Thinktomorrow\Squanto\Domain\Exceptions\InvalidMetadataArray;

/**
 * Class MetadataCollection
 *
 * @method null|Metadata find(string $key)
 * @method bool exists(string $key)
 */
final class MetadataCollection
{
    use KeyedCollection;

    private array $items;

    public function __construct(array $items)
    {
        $this->validateItems($items);

        $this->items = static::keyify($items);
    }

    public static function fromLines(Lines $lines): self
    {
        $collection = [];

        $lines->each(function(Line $line) use(&$collection){
            $collection[] = Metadata::fromLine($line);
        });

        return new static($collection);
    }

    public static function fromArray(array $items): self
    {
        $preppedItems = [];

        foreach($items as $key => $values) {
            $preppedItems[] = Metadata::fromRaw($key, $values);
        }

        return new static($preppedItems);
    }

    public function merge(MetadataCollection $metadataCollection): self
    {
        $mergedItems = $this->mergeByKey($this->items, $metadataCollection->items);

        return new static($mergedItems);
    }

    private function validateItems(array $items): void
    {
        foreach($items as $item){
            if(! $item instanceof Metadata) {
                throw new InvalidMetadataArray('Metadata items array should only consist of Metadata instances.');
            }
        }
    }
}
