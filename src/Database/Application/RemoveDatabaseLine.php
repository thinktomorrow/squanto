<?php
declare(strict_types=1);

namespace Thinktomorrow\Squanto\Database\Application;

use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\Squanto\Database\DatabaseLine;

final class RemoveDatabaseLine
{
    public function handle(Line $line): void
    {
        DatabaseLine::findByKey(LineKey::fromString($line->keyAsString()))->delete();
    }
}
