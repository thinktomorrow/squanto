<?php

declare(strict_types=1);

namespace Thinktomorrow\Squanto\Sources;

final class SquantoRegistry
{
    /**
     * @var array<string, SquantoSource>
     */
    private array $sources = [];

    public function registerSource(string $path, ?string $namespace = null, ?string $label = null): void
    {
        $source = SquantoSource::make($path, $namespace, $label);

        $this->sources[$source->uniqueKey()] = $source;
    }

    /**
     * @return array<int, SquantoSource>
     */
    public function all(): array
    {
        return array_values($this->sources);
    }

    public function defaultSource(): SquantoSource
    {
        return $this->all()[0];
    }
}
