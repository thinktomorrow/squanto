<?php

namespace Thinktomorrow\Squanto\Translators;

interface Translator
{
    /**
     * @param  $key
     * @param  array $replace
     * @param  null  $locale
     * @param  bool  $fallback
     * @return mixed
     */
    public function get($key, array $replace = [], $locale = null, $fallback = true);
}
