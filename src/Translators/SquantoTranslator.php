<?php

namespace Thinktomorrow\Squanto\Translators;

use Illuminate\Support\Facades\Schema;
use Illuminate\Translation\Translator as LaravelTranslator;
use Thinktomorrow\Squanto\Domain\PageKey;

class SquantoTranslator extends LaravelTranslator implements Translator
{
    private $databaseTranslator;

    private $keyAsDefault = true;
    private $isDatabaseAlreadyMigrated = null;

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

        // The key is always stored as lowercase so make sure our key input is sanitized as well.
        $key = strtolower($key);

        if( null !== ($result = $this->getFromExcludedSource($key, $replace, $locale, $fallback))) {
            return $result;
        }

        if ( null !== ($result = $this->getFromCache($key, $replace, $locale, $fallback))) {
            return $result;
        }

        if (null !== ($result = $this->getFromDatabase($key, $replace, $locale, $fallback))) {
            return $result;
        }

        $result = parent::get($key, $replace, $locale, $fallback);

        return ($this->keyAsDefault || $result !== $key) ? $result : null;
    }

    /**
     * Get from excluded sources. This is used here to make retrieval of these
     * non-managed translations a lot faster by going straight to source
     *
     * @param $key
     * @param $replace
     * @param $locale
     * @param $fallback
     * @return array|null|string
     */
    private function getFromExcludedSource($key, $replace, $locale, $fallback)
    {
        if(!PageKey::fromLineKeyString($key)->isExcludedSource()) return null;

        return parent::get($key, $replace, $locale, $fallback);
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
        /**
         * If database tables are not present we will soft ignore this call and delegate to the native
         */
        if (! $this->isDatabaseAlreadyMigrated()) {
            return null;
        }

        if (!isset($this->databaseTranslator)) {
            $this->databaseTranslator = app(DatabaseTranslator::class);
        }

        return $this->databaseTranslator->get($key, $replace, $locale, $fallback);
    }

    /**
     * Verify that SQUANTO migrations are already run and present in this environment
     * Allow for a soft install
     *
     * @return null
     */
    private function isDatabaseAlreadyMigrated()
    {
        if (!is_null($this->isDatabaseAlreadyMigrated)) {
            return $this->isDatabaseAlreadyMigrated;
        }

        return ($this->isDatabaseAlreadyMigrated = Schema::hasTable('squanto_lines'));
    }
}
