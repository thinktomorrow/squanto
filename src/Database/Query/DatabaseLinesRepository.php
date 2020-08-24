<?php
declare(strict_types=1);

namespace Thinktomorrow\Squanto\Database\Query;

use Illuminate\Support\Collection;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Domain\Lines;
use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\Squanto\Database\DatabaseLine;
use Thinktomorrow\Squanto\Domain\Exceptions\NotFoundDatabaseLine;
use Thinktomorrow\Squanto\Domain\Exceptions\InvalidLineKeyException;

final class DatabaseLinesRepository
{
    public function find(string $key): Line
    {
        if(!$model = DatabaseLine::findByKey(LineKey::fromString($key))) {
            throw new NotFoundDatabaseLine('No line in database found by key ' . $key);
        }

        return $model->toLine();
    }

    public function exists(string $key): bool
    {
        try{
            return null !== DatabaseLine::findByKey(LineKey::fromString($key));
        } catch(InvalidLineKeyException $e) {
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

    public function modelsStartingWith(string $key): Collection
    {
        return DatabaseLine::where('key', 'LIKE', $key . '%')->get();
    }
}
