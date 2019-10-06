<?php

namespace Thinktomorrow\Squanto\Translators;

use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\Squanto\Domain\PageKey;
use Thinktomorrow\Squanto\Domain\DatabaseLine;
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
        if (!$line = DatabaseLine::findByKey(LineKey::fromString($key))) {

            /** If no line is requested, we check if an entire page is asked for */
            if(PageKey::isValid($key) && $page = Page::findByKey(PageKey::fromString($key)))
            {
                $lines = DatabaseLine::getValuesByLocaleAndPage($locale ?? app()->getLocale(), PageKey::fromString($key));
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
