<?php

namespace Thinktomorrow\Squanto\Domain;

class Completion
{
    public static function getPageCompletion($pageid)
    {
        foreach (config('squanto.locales') as $locale) {
            if ((Integer)self::getPageCompletionPercentage($pageid, $locale) != 100) {
                return false;
            }
        }
        return true;
    }

    public static function getPageCompletionPercentage($pageid, $locale)
    {
        $page   = Page::find($pageid);
        $total  = $page->lines->count();

        if ($total == 0) {
            return 100;
        }

        $translated = collect(Line::getValuesByLocaleAndPage($locale, $page->key))->count();
        return $translated / $total * 100;
    }
}
