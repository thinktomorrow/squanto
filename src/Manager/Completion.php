<?php

namespace Thinktomorrow\Squanto\Domain;

use Thinktomorrow\Squanto\Database\DatabaseLine;
use Thinktomorrow\Squanto\Database\DatabasePage;

class Completion
{
    public static function check(DatabasePage $page)
    {
        foreach (config('thinktomorrow.squanto.locales') as $locale) {
            if ((Integer)self::asPercentage($page, $locale) != 100) {
                return false;
            }
        }
        return true;
    }

    public static function asPercentage(DatabasePage $page, $locale)
    {
        $total  = $page->lines->count();

        if ($total == 0) {
            return 100;
        }

        $translated = collect(DatabaseLine::getValuesByLocaleAndPage($locale, $page->key))->count();
        return $translated / $total * 100;
    }
}
