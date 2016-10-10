<?php

namespace Thinktomorrow\Squanto\Translators;

use Thinktomorrow\Squanto\Domain\Line;

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
            return null;
        }

        if (!$value = $line->getValue($locale, $fallback)) {
            return null;
        }

        foreach ($replace as $key => $replacer) {
            $value = str_replace(':'.$key, $replacer, $value);
        }

        return $value;
    }
}
