<?php

declare(strict_types=1);

namespace Thinktomorrow\Squanto\Domain;

use Thinktomorrow\Squanto\Domain\Exceptions\InvalidLineKeyException;

final class LineKey
{
    private string $key;

    private function __construct(string $key)
    {
        $this->validateKey($key);

        $this->key = $this->sanitizeKey($key);
    }

    public static function fromString(string $key)
    {
        return new self($key);
    }

    public function get(): string
    {
        return $this->key;
    }

    /**
     * Get Page identifier.
     * This is the first segment of the key
     *
     * @return string
     */
    public function pageKey(): string
    {
        return substr($this->key, 0, strpos($this->key, '.'));
    }

    public function equals($other): bool
    {
        return (get_class($this) === get_class($other) && $this->get() === $other->get());
    }

    private function sanitizeKey(string $key): string
    {
        return strtolower($key);
    }

    public static function validateKey($key): void
    {
        if (!$key || !is_string($key) || false === strpos($key, '.')) {
            throw new InvalidLineKeyException('Invalid LineKey format ['.$key.', type: '.gettype($key).'] given. Must be a string containing at least two dot separated segments. E.g. about.title');
        }
    }

}
