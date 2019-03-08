<?php

namespace Thinktomorrow\Squanto\Domain;

class Completion
{
    public static function check(Page $page)
    {
        foreach (config('squanto.locales') as $locale) {
            if ((Integer)self::asPercentage($page, $locale) != 100) {
                return false;
            }
        }
        return true;
    }

    public static function asPercentage(Page $page, $locale)
    {
        $total  = $page->lines->count();

        if ($total == 0) {
            return 100;
        }

        $translated = collect(DatabaseLine::getValuesByLocaleAndPage($locale, $page->key))->count();
        return $translated / $total * 100;
    }
}
