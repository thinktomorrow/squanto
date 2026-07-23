<?php

declare(strict_types=1);

namespace Thinktomorrow\Squanto\Domain;

final class TranslationTarget
{
    private function __construct(private string $target)
    {
    }

    public static function fromString(?string $target): self
    {
        $target = trim((string) $target);

        return new self($target !== '' ? strtolower($target) : 'all');
    }

    public function value(): string
    {
        return $this->target;
    }

    public function keyMatchDescription(): string
    {
        if ($this->target === 'all') {
            return '*';
        }

        if ($this->target === 'app') {
            return 'non-namespaced keys';
        }

        return $this->target.'.*';
    }

    public function matches(string $key): bool
    {
        $key = strtolower($key);

        if ($this->target === 'all') {
            return true;
        }

        if ($this->target === 'app') {
            return ! str_contains($key, '::');
        }

        return $key === $this->target
            || str_starts_with($key, $this->target.'.')
            || str_starts_with($key, $this->target.'::');
    }

    public function filter(Lines $lines): Lines
    {
        return $lines->filter(fn (Line $line) => $this->matches($line->keyAsString()));
    }
}
