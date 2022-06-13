<?php

namespace Thinktomorrow\Squanto\Manager\Pages;

use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Domain\Lines;

final class Completion
{
    public function calculate(Lines $lines, array $locales): int
    {
        $maximum = $lines->count() * count($locales);
        $completed = 0;

        if ($maximum == 0) {
            return 100;
        }

        $lines->each(function (Line $line) use ($locales, &$completed) {
            foreach ($locales as $locale) {
                if (null !== $line->value($locale) && '' !== $line->value($locale)) {
                    $completed++;
                }
            }
        });

        return round($completed / $maximum, 2) * 100;
    }
}
