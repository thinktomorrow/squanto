<?php
declare(strict_types=1);

namespace Thinktomorrow\Squanto\Services;

trait KeyedCollection
{
    public function find(string $key)
    {
        if (! $this->exists($key)) {
            return null;
        }

        return $this->items[$key];
    }

    public function exists(string $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    private function mergeByKey(array $items, array $otherItems): array
    {
        $mergedItems = $items;

        foreach ($otherItems as $otherItem) {
            if ($this->exists($otherItem->keyAsString())) {
                $mergedItems[] = $this->items[$otherItem->keyAsString()]->merge($otherItem);
            } else {
                $mergedItems[] = $otherItem;
            }
        }

        return $mergedItems;
    }

    /**
     * Use the key of each Line as key for its array entry.
     * This way we have unique entries and allow for easy replacement of existing values.
     *
     * @param  array $items
     * @return array
     */
    private static function keyify(array $items): array
    {
        $result = [];

        foreach ($items as $item) {
            $result[$item->keyAsString()] = $item;
        }

        return $result;
    }
}
