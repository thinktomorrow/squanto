<?php

namespace Thinktomorrow\Squanto\Domain;

use Thinktomorrow\Squanto\Exceptions\InvalidLineKeyException;

class LineKey
{
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
        // Remove first part since that part equals the page
        $key = substr($this->key, strpos($this->key, '.')+1);

        $label = str_replace('.', ' ', $key);

        return ucfirst($label);
    }

    /**
     * Get Page identifier.
     * This is the first segment of the key
     *
     * @return string
     */
    public function getPageKey()
    {
        return substr($this->key, 0, strpos($this->key, '.'));
    }

    private function sanitizeKey($key)
    {
        return strtolower($key);
    }

    private function validateKey($key)
    {
        if (!$key || !is_string($key) || false === strpos($key, '.')) {
            throw new InvalidLineKeyException('Invalid LineKey format ['.$key.', type: '.gettype($key).'] given. Must be a string containing at least two dot separated segments. E.g. about.title');
        }
    }

}
