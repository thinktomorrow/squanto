<?php

declare(strict_types=1);

namespace Thinktomorrow\Squanto\Disk;

use Thinktomorrow\Squanto\Domain\Lines;
use Thinktomorrow\Squanto\Sources\SquantoRegistry;

final class DiskLinesRepository
{
    private ReadLanguageFolder $readLanguageFolder;

    private SquantoRegistry $registry;

    public function __construct(ReadLanguageFolder $readLanguageFolder, SquantoRegistry $registry)
    {
        $this->readLanguageFolder = $readLanguageFolder;
        $this->registry = $registry;
    }

    public function all(): Lines
    {
        $locales = config('squanto.locales');

        $lines = new Lines([]);

        foreach ($this->registry->all() as $source) {
            foreach ($locales as $locale) {
                $lines = $lines->merge($this->readLanguageFolder->read($locale, $source));
            }
        }

        return $lines;
    }
}
