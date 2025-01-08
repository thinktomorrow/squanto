<?php

namespace Thinktomorrow\Squanto\Translators;

interface Translator
{
    /**
     * @param  string $key
     * @param  array $replace
     * @param  null  $locale
     * @param  bool  $fallback
     * @return string|array|null
     */
    public function get($key, array $replace = [], $locale = null, $fallback = true);
}
