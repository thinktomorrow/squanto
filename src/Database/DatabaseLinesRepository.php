<?php

declare(strict_types=1);

namespace Thinktomorrow\Squanto\Database;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Thinktomorrow\Squanto\Domain\Exceptions\InvalidLineKeyException;
use Thinktomorrow\Squanto\Domain\Exceptions\NotFoundDatabaseLine;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\Squanto\Domain\Lines;

final class DatabaseLinesRepository
{
    public function find(string $key): Line
    {
        if (! $model = DatabaseLine::findByKey(LineKey::fromString($key))) {
            throw new NotFoundDatabaseLine('No line in database found by key '.$key);
        }

        return $model->toLine();
    }

    public function exists(string $key): bool
    {
        try {
            return DatabaseLine::findByKey(LineKey::fromString($key)) !== null;
        } catch (InvalidLineKeyException $e) {
            return false;
        }
    }

    public function all(): Lines
    {
        $lines = DatabaseLine::all()->map->toLine()->all();

        return new Lines($lines);
    }

    public function allStartingWith(string $key): Lines
    {
        $models = $this->modelsStartingWith($key)->map->toLine()->all();

        return new Lines($models);
    }

    public function allForPage(string $pageKey): Lines
    {
        $models = $this->modelsForPage($pageKey)->map->toLine()->all();

        return new Lines($models);
    }

    public function modelsStartingWith(string $key): Collection
    {
        return DatabaseLine::where('key', 'LIKE', $key.'%')->get();
    }

    public function modelsForPage(string $pageKey): Collection
    {
        return DatabaseLine::where('key', 'LIKE', $pageKey.'.%')->get();
    }

    public function search(string $term): Collection
    {
        $searchTerm = trim($term);

        if ($searchTerm === '') {
            return collect();
        }

        $needle = Str::lower($searchTerm);

        return DatabaseLine::query()
            ->get()
            ->filter(fn (DatabaseLine $line) => $this->lineMatchesSearch($line, $needle))
            ->values();
    }

    private function lineMatchesSearch(DatabaseLine $line, string $needle): bool
    {
        foreach ($this->searchableStrings($line) as $value) {
            if (Str::contains(Str::lower($value), $needle)) {
                return true;
            }
        }

        return false;
    }

    private function searchableStrings(DatabaseLine $line): array
    {
        $values = [$line->key];

        $lineValues = $line->toLine()->values();

        array_walk_recursive($lineValues, function (mixed $value) use (&$values) {
            if (is_scalar($value)) {
                $values[] = (string) $value;
            }
        });

        return $values;
    }
}
