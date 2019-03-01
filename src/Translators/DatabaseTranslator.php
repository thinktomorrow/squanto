<?php

namespace Thinktomorrow\Squanto\Translators;

use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Domain\Page;
use Thinktomorrow\Squanto\Services\ConvertToTree;

class DatabaseTranslator implements Translator
{
    /**
     * Get translation for given key from database.
     *
     * @param $key
     * @param array $replace
     * @param null $locale
     * @param bool $fallback
     * @return mixed|null
     */
    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        if (!$line = Line::findByKey($key)) {

            /**
             * If no line is requested,
             * we check if an entire page is asked for
             */
            if($page = Page::findByKey($key))
            {
                $lines = Line::getValuesByLocaleAndPage($locale, $key);
                return ConvertToTree::fromFlattened($lines, false);
            }

            return null;
        }

        // Return null or '' as is, because null will result in trying to fetch the translation
        // from the file source and an intentional empty string does not.
        if (!$value = $line->getValue($locale, $fallback)) {
            return $value;
        }

        foreach ($replace as $key => $replacer) {
            $value = str_replace(':'.$key, $replacer, $value);
        }

        return $value;
    }
}
