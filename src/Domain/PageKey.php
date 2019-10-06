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

        return new static($key);
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
        $label = str_replace(['.','-'], ' ', $this->key);

        return ucfirst($label);
    }

    public function isExcludedSource()
    {
        $excluded = $this->getExcludedSources();

        return in_array($this->key,$excluded);
    }

    public function equals($other)
    {
        return (get_class($this) === get_class($other) && $this->get() === $other->get());
    }

    private function sanitizeKey($key)
    {
        return strtolower($key);
    }

    public static function isValid($key): bool
    {
        try{
            new static($key);
            return true;
        } catch(InvalidPageKeyException $e) {
            return false;
        }
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
