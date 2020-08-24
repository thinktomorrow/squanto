<?php
declare(strict_types=1);

namespace Thinktomorrow\Squanto\Database\Application;

use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Domain\Metadata\Metadata;
use Thinktomorrow\Squanto\Database\DatabaseLine;

final class AddDatabaseLine
{
    public function handle(Line $line, Metadata $metadata = null): void
    {
        DatabaseLine::create([
            'key'      => $line->keyAsString(),
            'values'   => ['value' => $line->values()],
            'metadata' => $metadata ? $metadata->values() : [],
        ]);
    }
}
