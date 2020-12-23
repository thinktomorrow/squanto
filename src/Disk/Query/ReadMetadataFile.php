<?php
declare(strict_types=1);

namespace Thinktomorrow\Squanto\Disk\Query;

use Illuminate\Support\Arr;
use Thinktomorrow\Squanto\Domain\Metadata\MetadataCollection;
use Thinktomorrow\Squanto\Domain\Exceptions\InvalidMetadataFileReference;

final class ReadMetadataFile
{
    public function read(string $filepath): MetadataCollection
    {
        if(!is_file($filepath) || !file_exists($filepath)) {
            throw new InvalidMetadataFileReference('Filepath ['.$filepath . '] does not point to an existing or valid language file.');
        }

        // Parse filepath and get all metadata lines as an array
        $lines = include $filepath;

        return MetadataCollection::fromArray(static::flattenAndPrependGroupKey($lines, $filepath));
    }

    private static function flattenAndPrependGroupKey(array $values, string $filepath): array
    {
        $dottedValues = Arr::dot($values);

        $filename = pathinfo($filepath)['filename'];
        $groupKey = strtolower($filename);

        // Ok so here's the deal, we want to flatten but not everything. The metadata values are an array
        // and should be treated together under the same key. Therefore we must be sure to group all values together
        $groupedDottedValues = [];
        foreach($dottedValues as $dottedKey => $dottedValue)
        {
            $realKey = $groupKey.'.'.substr($dottedKey, 0, strrpos($dottedKey, '.'));
            $valueKey = substr($dottedKey, strrpos($dottedKey, '.') + 1);
            if(!isset($groupedDottedValues[$realKey])) {
                $groupedDottedValues[$realKey] = [];
            }
            $groupedDottedValues[$realKey][$valueKey] = $dottedValue;
        }

        return $groupedDottedValues;
    }
}
