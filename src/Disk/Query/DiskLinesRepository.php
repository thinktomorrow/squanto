<?php
declare(strict_types=1);

namespace Thinktomorrow\Squanto\Disk\Query;

use Thinktomorrow\Squanto\Domain\Lines;

final class DiskLinesRepository
{
    /** @var ReadLanguageFolder */
    private ReadLanguageFolder $readLanguageFolder;

    public function __construct(ReadLanguageFolder $readLanguageFolder)
    {
        $this->readLanguageFolder = $readLanguageFolder;
    }

    public function all(): Lines
    {
        $locales = config('thinktomorrow.squanto.locales');

        $lines = new Lines([]);

        foreach($locales as $locale){
            $lines = $lines->merge($this->readLanguageFolder->read($locale));
        }

        return $lines;
    }
}
