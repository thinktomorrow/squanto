<?php

namespace Thinktomorrow\Squanto\Domain;

class Completion
{
    public static function getPageCompletion($pageid)
    {
        $page = Page::find($pageid);
        $total = $page->lines->count();
        $results = collect([]);

        foreach (config('squanto.locales') as $locale) {
            $results->push(collect(Line::getValuesByLocaleAndPage($locale, $page->key))->count());
        }

        return $results->contains($total);
    }

    public static function getPageCompletionPerLocale($pageid, $locale)
    {
        $page   = Page::find($pageid);
        $total  = $page->lines->where('locale', $locale)->count();

        $results->push(collect(Line::getValuesByLocaleAndPage($locale, $page->key))->count());

        return $results->contains($total);
    }
}
