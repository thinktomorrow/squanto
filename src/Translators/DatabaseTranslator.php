<?php

namespace Thinktomorrow\Squanto\Translators;

use Illuminate\Support\Str;
use Thinktomorrow\Squanto\Database\DatabaseLinesRepository;
use Thinktomorrow\Squanto\Services\ConvertToTree;

class DatabaseTranslator implements Translator
{
    private static string $key_separator = '.';

    private DatabaseLinesRepository $databaseLinesRepository;

    public function __construct(DatabaseLinesRepository $databaseLinesRepository)
    {
        $this->databaseLinesRepository = $databaseLinesRepository;
    }

    /**
     * Get translation for given key from database.
     */
    public function get(string $key, array $replace = [], ?string $locale = null, bool $fallback = true): string|array|null
    {
        if (! $this->databaseLinesRepository->exists($key)) {

            $lines = $this->databaseLinesRepository->allStartingWith($key. self::$key_separator);

            /**
             * If no specific line is requested, we check if a collection of lines can be retrieved
             */
            if ($lines->count() > 0) {

                $flattenedValues = $lines->values($locale ?: app()->getLocale());

                foreach ($flattenedValues as $k => $flattenedValue) {
                    if (Str::startsWith($k, $key)) {
                        $removalPrefix = substr($k, 0, strlen($key));

                        $newKey = trim(str_replace($removalPrefix, '', $k), self::$key_separator);
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

        if (! $value && $fallback && config('app.fallback_locale')) {
            $value = $line->value(config('app.fallback_locale'));
        }

        // Return null or '' as is, because null will result in trying to fetch the translation
        // from the file source and an intentional empty string does not.
        if (! $value) {
            return $value;
        }

        foreach ($replace as $key => $replacer) {
            $value = str_replace(':'.$key, $replacer, $value);
        }

        return $value;
    }
}
