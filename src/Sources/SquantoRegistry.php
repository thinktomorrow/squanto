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

    public function findByNamespace(?string $namespace): ?SquantoSource
    {
        foreach ($this->all() as $source) {
            if ($source->namespace() === $namespace) {
                return $source;
            }
        }

        return null;
    }
}
