<?php

declare(strict_types=1);

namespace Thinktomorrow\Squanto\Sources;

use Illuminate\Support\Str;

final class SquantoSource
{
    private function __construct(
        private string $path,
        private ?string $namespace,
        private string $label,
    ) {
    }

    public static function make(string $path, ?string $namespace = null, ?string $label = null): self
    {
        $normalizedNamespace = $namespace !== null ? trim($namespace) : null;

        if ($normalizedNamespace === '') {
            $normalizedNamespace = null;
        }

        return new self(
            rtrim($path, '/'),
            $normalizedNamespace,
            $label !== null && trim($label) !== '' ? trim($label) : ($normalizedNamespace ?: 'App')
        );
    }

    public function path(): string
    {
        return $this->path;
    }

    public function namespace(): ?string
    {
        return $this->namespace;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function isNamespaced(): bool
    {
        return $this->namespace !== null;
    }

    public function uniqueKey(): string
    {
        return ($this->namespace ?: 'app').'|'.$this->path;
    }

    public function groupKey(string $filename): string
    {
        $groupKey = Str::lower($filename);

        if (! $this->isNamespaced()) {
            return $groupKey;
        }

        return $this->namespace.'::'.$groupKey;
    }

    public function translationKey(string $filename, string $nestedKey): string
    {
        return $this->groupKey($filename).'.'.$nestedKey;
    }
}
