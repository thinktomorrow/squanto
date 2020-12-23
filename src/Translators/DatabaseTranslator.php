<?php

namespace Thinktomorrow\Squanto\Translators;

use Illuminate\Support\Str;
use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\Squanto\Database\DatabaseLine;
use Thinktomorrow\Squanto\Services\ConvertToTree;
use Thinktomorrow\Squanto\Database\Query\DatabaseLinesRepository;

class DatabaseTranslator implements Translator
{
    /**
     * @var DatabaseLinesRepository 
     */
    private DatabaseLinesRepository $databaseLinesRepository;

    public function __construct(DatabaseLinesRepository $databaseLinesRepository)
    {
        $this->databaseLinesRepository = $databaseLinesRepository;
    }

    /**
     * Get translation for given key from database.
     *
     * @param  $key
     * @param  array $replace
     * @param  null  $locale
     * @param  bool  $fallback
     * @return mixed|null
     */
    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        if(!$this->databaseLinesRepository->exists($key)) {
            /**
 * If no specific line is requested, we check if a collection of lines can be retrieved 
*/
            if(($lines = $this->databaseLinesRepository->allStartingWith($key)) && $lines->count() > 0) {

                $flattenedValues = $lines->values($locale ?? app()->getLocale());

                foreach($flattenedValues as $k => $flattenedValue) {
                    if(Str::startsWith($k, $key)) {
                        $removalPrefix = substr($k, 0, strlen($key));
                        $newKey = trim(str_replace($removalPrefix, '', $k), '.');
                        unset($flattenedValues[$k]);

                        $flattenedValues[$newKey] = $flattenedValue;
                    }
                }

                return ConvertToTree::fromFlattened($flattenedValues);
            }

            return null;
        }

        $line = $this->databaseLinesRepository->find($key);

        $value = $line->value($locale ?? app()->getLocale());

        //trap($line->value($locale ?? app()->getLocale()), $locale ?? app()->getLocale(), $fallback);

        if (!$value && $fallback && config('app.fallback_locale')) {
            $value = $line->value(config('app.fallback_locale'));
        }

        // Return null or '' as is, because null will result in trying to fetch the translation
        // from the file source and an intentional empty string does not.
        if(!$value) { return $value;
        }

        foreach ($replace as $key => $replacer) {
            $value = str_replace(':'.$key, $replacer, $value);
        }

        return $value;
    }
}
