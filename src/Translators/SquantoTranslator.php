<?php

namespace Thinktomorrow\Squanto\Translators;

use Illuminate\Translation\Translator as LaravelTranslator;

class SquantoTranslator extends LaravelTranslator implements Translator
{
    private $databaseTranslator;

    private $keyAsDefault = true;

    public function setKeyAsDefault($keyAsDefault = true)
    {
        $this->keyAsDefault = $keyAsDefault;
    }

    /**
     * Get the translation for the given key by following this priority chain:
     *
     * 1. Get from our cached translations
     * 2. Get from database
     * 3. Get from the /resources/lang
     *
     * @param  string $key
     * @param  array $replace
     * @param  string $locale
     * @param bool $fallback
     * @return string
     */
    public function get($key, array $replace = array(), $locale = null, $fallback = true)
    {
        $locale = $locale ?: $this->getLocale();

        if ($result = $this->getFromCache($key, $replace, $locale, $fallback)) {
            return $result;
        }

        if ($result = $this->getFromDatabase($key, $replace, $locale, $fallback)) {
            return $result;
        }

        $result = parent::get($key, $replace, $locale, $fallback);

        return ($this->keyAsDefault || $result !== $key) ? $result : null;
    }

    /**
     * Retrieve the translation from the squanto cache.
     *
     * @param $key
     * @param array $replace
     * @param null $locale
     * @return mixed|null
     */
    private function getFromCache($key, array $replace = array(), $locale = null, $fallback = true)
    {
        if (false === strpos($key, 'squanto::')) {
            $key = 'squanto::'.$key;
        }

        $result = parent::get($key, $replace, $locale, $fallback);

        return ($result !== $key) ? $result : null;
    }

    private function getFromDatabase($key, array $replace = array(), $locale = null, $fallback = true)
    {
        if (!isset($this->databaseTranslator)) {
            $this->databaseTranslator = app(DatabaseTranslator::class);
        }

        return $this->databaseTranslator->get($key, $replace, $locale, $fallback);
    }
}
