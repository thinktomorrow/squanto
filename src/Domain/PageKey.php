<?php

namespace Thinktomorrow\Squanto\Domain;

use Thinktomorrow\Squanto\Exceptions\InvalidPageKeyException;

class PageKey
{
    private static $excludedSources;
    private $key;

    public function __construct($key)
    {
        $this->validateKey($key);

        $this->key = $this->sanitizeKey($key);
    }

    public static function fromString($key)
    {
        return new self($key);
    }

    public static function fromLineKeyString($key)
    {
        $key = false !== strpos($key,'.') ? substr($key, 0, strpos($key, '.')) : $key;
        return new self($key);
    }

    public function get()
    {
        return $this->key;
    }

    /**
     * Get suggestion for a label based on the key
     *
     * @return string
     */
    public function getAsLabel()
    {
        return ucfirst($this->key);
    }

    public function isExcludedSource()
    {
        $excluded = $this->getExcludedSources();

        return in_array($this->key,$excluded);
    }

    private function sanitizeKey($key)
    {
        return strtolower($key);
    }

    private function validateKey($key)
    {
        if (!$key || !is_string($key) || false !== strpos($key, '.')) {
            throw new InvalidPageKeyException('Invalid PageKey format ['.$key.', type: '.gettype($key).'] given. Must be a string without dot separated segments. E.g. about and not about.title');
        }
    }

    private function getExcludedSources()
    {
        if(!self::$excludedSources) {
            self::$excludedSources = config('squanto.excluded_files',[]);
        }

        return self::$excludedSources;
    }

    public static function refreshExcludedSources()
    {
        self::$excludedSources = null;
    }

}
