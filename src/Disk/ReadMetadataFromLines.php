<?php
declare(strict_types=1);

namespace Thinktomorrow\Squanto\Disk;

use Thinktomorrow\Squanto\Domain\Lines;
use Thinktomorrow\Squanto\Domain\Metadata\MetadataCollection;

final class ReadMetadataFromLines
{
    public function read(Lines $lines): MetadataCollection
    {
        // todo: make sure that Lines are always reading the primary locale first, so the sequence (order) of lines can be managed in these locale files
        return MetadataCollection::fromLines($lines);
    }
}
