<?php

namespace Thinktomorrow\Squanto\Translators;

use Illuminate\Contracts\Translation\Loader;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Translation\Translator as LaravelTranslator;
use Thinktomorrow\Squanto\Disk\Paths;

class SquantoTranslator extends LaravelTranslator implements Translator
{
    private DatabaseTranslator $databaseTranslator;

    private array $excludedFilenames;

    private bool $keyAsDefault = true;

    private ?bool $isDatabaseAlreadyMigrated = null;

    public function __construct(Loader $loader, $locale)
    {
        parent::__construct($loader, $locale);

        $this->excludedFilenames = config('squanto.excluded_files', []);
        $this->databaseTranslator = app(DatabaseTranslator::class);
    }

    /**
     * In case the translation is not found, Laravel returns the transkey by default.
     * In squanto config you could choose to opt out of this behavior and instead
     * have a non existing translation return null.
     */
    public function setKeyAsDefault(bool $keyAsDefault = true): void
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
     * @param  string  $key
     * @param  null  $locale
     * @param  bool  $fallback
     * @return string|array|null
     */
    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        $locale = $locale ?: $this->getLocale();

        // The key is always stored as lowercase so make sure our key input is sanitized as well.
        $key = strtolower($key);

        if (null !== ($result = $this->getFromExcludedSource($key, $replace, $locale, $fallback))) {
            return $result;
        }

        if (null !== ($result = $this->getFromCache($key, $replace, $locale, $fallback))) {
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
     * @return array|null|string
     */
    private function getFromExcludedSource($key, $replace, $locale, $fallback)
    {
        if (! $this->belongsToExcludedSource($key)) {
            return null;
        }

        return parent::get($key, $replace, $locale, $fallback);
    }

    private function belongsToExcludedSource(string $key): bool
    {
        if (! Str::contains($key, '.')) {
            return false;
        }

        $pagePrefix = Str::before($key, '.');

        return in_array($pagePrefix, $this->excludedFilenames);
    }

    /**
     * Retrieve the translation from the Squanto cache.
     *
     * @param  null  $locale
     * @return mixed|null
     */
    private function getFromCache($key, array $replace = [], $locale = null, $fallback = true)
    {
        foreach ($this->cacheLocales($locale, $fallback) as $cacheLocale) {
            if (null !== ($result = $this->getFromCacheLocale($key, $replace, $cacheLocale))) {
                return $result;
            }
        }

        return null;
    }

    private function getFromCacheLocale(string $key, array $replace, string $locale)
    {
        [$namespace, $lineKey] = $this->splitNamespace($key);
        $group = Str::before($lineKey, '.');
        $item = Str::contains($lineKey, '.') ? Str::after($lineKey, '.') : null;
        $filepath = $this->cacheFilepath($namespace, $locale, $group);

        if (! is_file($filepath)) {
            return null;
        }

        $lines = include $filepath;

        if (! is_array($lines)) {
            return null;
        }

        if ($item === null) {
            return $lines;
        }

        if (! Arr::has($lines, $item)) {
            return null;
        }

        return $this->makeReplacements(Arr::get($lines, $item), $replace);
    }

    private function cacheLocales(string $locale, bool $fallback): array
    {
        $locales = [$locale];

        if ($fallback && config('app.fallback_locale')) {
            $locales[] = config('app.fallback_locale');
        }

        return array_values(array_unique($locales));
    }

    private function cacheFilepath(?string $namespace, string $locale, string $group): string
    {
        if ($namespace) {
            return Paths::getSquantoCachePath().'/vendor/'.$namespace.'/'.$locale.'/'.$group.'.php';
        }

        return Paths::getSquantoCachePath().'/'.$locale.'/'.$group.'.php';
    }

    private function splitNamespace(string $key): array
    {
        if (! Str::contains($key, '::')) {
            return [null, $key];
        }

        return explode('::', $key, 2);
    }

    private function getFromDatabase($key, array $replace = [], $locale = null, $fallback = true)
    {
        /**
         * If database tables are not present we will soft ignore this call and delegate to the native
         */
        if (! $this->isDatabaseAlreadyMigrated()) {
            return null;
        }

        return $this->databaseTranslator->get($key, $replace, $locale, $fallback);
    }

    /**
     * Verify that SQUANTO migrations are already run and present in this environment
     * Allow for a soft install
     */
    private function isDatabaseAlreadyMigrated(): bool
    {
        if (! is_null($this->isDatabaseAlreadyMigrated)) {
            return $this->isDatabaseAlreadyMigrated;
        }

        return $this->isDatabaseAlreadyMigrated = Schema::hasTable('squanto_lines');
    }
}
