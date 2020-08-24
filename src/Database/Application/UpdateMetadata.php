<?php
declare(strict_types=1);

namespace Thinktomorrow\Squanto\Database\Application;

use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\Squanto\Domain\Metadata\Metadata;
use Thinktomorrow\Squanto\Database\DatabaseLine;

final class UpdateMetadata
{
    public function handle(LineKey $lineKey, Metadata $metadata): void
    {
        if(!$line = DatabaseLine::findByKey($lineKey)) {
            throw new \InvalidArgumentException('No database line found for linekey ' . $lineKey->get());
        }

        $line->update(['metadata' => $metadata->values(),]);
    }
}
