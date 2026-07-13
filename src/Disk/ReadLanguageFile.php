<?php

declare(strict_types=1);

namespace Thinktomorrow\Squanto\Disk;

use Illuminate\Support\Arr;
use Thinktomorrow\Squanto\Domain\Exceptions\InvalidLanguageFileReference;
use Thinktomorrow\Squanto\Domain\Lines;
use Thinktomorrow\Squanto\Sources\SquantoRegistry;
use Thinktomorrow\Squanto\Sources\SquantoSource;

final class ReadLanguageFile
{
    public function __construct(private SquantoRegistry $registry)
    {
    }

    public function read(string $locale, string $filepath, ?SquantoSource $source = null): Lines
    {
        if (! is_file($filepath) || ! file_exists($filepath)) {
            throw new InvalidLanguageFileReference('Filepath ['.$filepath.'] does not point to an existing or valid language file.');
        }

        // Parse filepath and get all lines as array ...
        $lines = include $filepath;

        if (! is_array($lines)) {
            throw new InvalidLanguageFileReference('Content of a language file should return an array. filepath: ['.$filepath.']');
        }

        return Lines::fromArray($locale, $this->flattenAndPrependGroupKey($lines, $filepath, $source));
    }

    private function flattenAndPrependGroupKey(array $values, string $filepath, ?SquantoSource $source = null): array
    {
        $dottedValues = Arr::dot($values);

        $filename = pathinfo($filepath)['filename'];
        $source = $source ?: $this->registry->defaultSource();

        $combinedValues = [];
        foreach ($dottedValues as $key => $value) {
            $combinedValues[$source->translationKey($filename, $key)] = $value;
        }

        return $combinedValues;
    }
}
