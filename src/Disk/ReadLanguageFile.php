<?php
declare(strict_types=1);

namespace Thinktomorrow\Squanto\Disk;

use Illuminate\Support\Arr;
use Thinktomorrow\Squanto\Domain\Exceptions\InvalidLanguageFileReference;
use Thinktomorrow\Squanto\Domain\Lines;

final class ReadLanguageFile
{
    public function read(string $locale, string $filepath): Lines
    {
        if (! is_file($filepath) || ! file_exists($filepath)) {
            throw new InvalidLanguageFileReference('Filepath ['.$filepath . '] does not point to an existing or valid language file.');
        }

        // Parse filepath and get all lines as array ...
        $lines = include $filepath;

        if (! is_array($lines)) {
            throw new InvalidLanguageFileReference('Content of a language file should return an array. filepath: ['.$filepath.']');
        }

        return Lines::fromArray($locale, static::flattenAndPrependGroupKey($lines, $filepath));
    }

    private static function flattenAndPrependGroupKey(array $values, string $filepath): array
    {
        $dottedValues = Arr::dot($values);

        $filename = pathinfo($filepath)['filename'];
        $groupKey = strtolower($filename);

        $combinedValues = [];
        foreach ($dottedValues as $key => $value) {
            $combinedValues[$groupKey.'.'.$key] = $value;
        }

        return $combinedValues;
    }
}
