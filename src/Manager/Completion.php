<?php

namespace Thinktomorrow\Squanto\Manager;

use Illuminate\Support\Collection;
use Thinktomorrow\Squanto\Domain\Lines;
use Thinktomorrow\Squanto\Database\DatabaseLine;

final class Completion
{
    /** @var Lines */
    private Lines $lines;

    public function __construct(Lines $lines)
    {
        $this->lines = $lines;
    }

    public static function fromDatabaseLines(Collection $databaseLines): self
    {
        return new static( new Lines($databaseLines->map->toLine()->all() ));
    }

    public function asPercentage(string $locale): float
    {
        $total = $this->lines->count();
        $values = array_filter($this->lines->values($locale),function($value){ return null !== $value; });

        return round(count($values) / $total, 2);
    }

    public function isComplete(string $locale): bool
    {
        $total = $this->lines->count();
        $values = array_filter($this->lines->values($locale),function($value){ return null !== $value; });

        return count($values) == $total;
    }

    public function isCompleteForAllLocales(): bool
    {
        foreach(config('thinktomorrow.squanto.locales', []) as $locale) {
            if(!$this->isComplete($locale)) {
                return false;
            }
        }

        return true;
    }
}
