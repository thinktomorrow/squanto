<?php

namespace Thinktomorrow\Squanto\Manager\Http\Controllers;

use Illuminate\Http\Request;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Domain\Page;
use Thinktomorrow\Squanto\Services\CachedTranslationFile;

class TranslationController extends Controller
{
    public function index()
    {
        $pages = Page::sequence()->get();

        return view('squanto::index', compact('pages'));
    }

    public function edit($id)
    {
        $available_locales = config('squanto.locales');

        $page = Page::find($id);

        $groupedLines = $this->groupLinesByKey($page);

        return view('squanto::edit', compact('page', 'available_locales', 'groupedLines'));
    }

    public function update(Request $request, $page_id)
    {
        $page = Page::find($page_id);

        $this->saveValueTranslations($request->get('trans'));

        // Rebuild the translations cache
        app(CachedTranslationFile::class)->delete()->write();

        return redirect()->route('squanto.edit', $page->id)->with('messages.success', $page->label .' translations have been updated');
    }

    private function saveValueTranslations(array $translations)
    {
        collect($translations)->map(function ($translation, $locale) {
            collect($translation)->map(function ($value, $id) use ($locale) {

                $line = Line::find($id);
                $value = squantoCleanupHTML($value);

                if(false == config('squanto.paragraphize') && !$line->areParagraphsAllowed())
                {
                    $value = $this->replaceParagraphsByLinebreaks($value);
                }

                // If line value is not meant to contain tags, we should strip them
                if (!$line->editInEditor()) {
                    $value = squantoCleanupString($value);
                }

                if (null === $value) {
                    $line->removeValue($locale);
                } else {
                    $line->saveValue($locale, $value);
                }
            });
        });
    }

    /**
     * @param $page
     * @return \Illuminate\Support\Collection
     */
    protected function groupLinesByKey($page)
    {
        $groupedLines = collect(['general' => []]);
        $groups = [];

        foreach ($page->lines as $line) {
            $keysegment = $this->getFirstSegmentOfKey($line);

            if (!isset($groups[$keysegment])) {
                $groups[$keysegment] = [];
            }
            $groups[$keysegment][] = $line;
        }

        // If firstkey occurs more than once, we will group it
        foreach ($groups as $group => $lines) {
            if (count($lines) < 2) {
                $groupedLines['general'] = array_merge($groupedLines['general'], $lines);
            } else {
                $groupedLines[$group] = $lines;
            }
        }

        return $groupedLines;
    }

    /**
     * Get suggestion for a label based on the key
     * e.g. foo.bar.title return bar
     * @return string
     */
    private function getFirstSegmentOfKey(Line $line)
    {
        // Remove first part since that part equals the page
        $key = substr($line->key, strpos($line->key, '.')+1);

        $length = strpos($key, '.')?: strlen($key);
        $key = substr($key, 0, $length);

        return $key;
    }

    private function replaceParagraphsByLinebreaks($value)
    {
        $value = preg_replace('/<p[^>]*?>/', '', $value);

        // Last paragraph is just removed, not a linebreak
        if (substr($value, -mb_strlen('</p>')) === '</p>') {
            $value = substr($value, 0, -mb_strlen('</p>'));
        }

        $value = str_replace('</p>', '<br>', $value);

        return $value;
    }
}
