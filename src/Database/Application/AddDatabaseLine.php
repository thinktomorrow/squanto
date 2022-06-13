<?php
declare(strict_types=1);

namespace Thinktomorrow\Squanto\Database\Application;

use Thinktomorrow\Squanto\Database\DatabaseLine;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\Squanto\Domain\Metadata\Metadata;

final class AddDatabaseLine
{
    public function handle(Line $line, Metadata $metadata = null): void
    {
        // If the key exists as soft deleted entry, we'll first remove the former existing record
        if ($existingSoftDeletedLine = DatabaseLine::findSoftDeletedByKey(LineKey::fromString($line->keyAsString()))) {
            $existingSoftDeletedLine->forceDelete();
        }

        DatabaseLine::create(
            [
            'key' => $line->keyAsString(),
            'values' => ['value' => $line->values()],
            'metadata' => $metadata ? $metadata->values() : [],
            ]
        );
    }
}
