<?php

namespace Thinktomorrow\Squanto\Application\Commands;

use Thinktomorrow\Squanto\Domain\DatabaseLine;
use Thinktomorrow\Squanto\Domain\LineKey;

class AddDatabaseLine
{
    public function handle(LineKey $lineKey, array $pairs)
    {
        // Line should not exist yet...
        if(DatabaseLine::findByKey($lineKey)) return;

        $databaseLine = DatabaseLine::createFromKey($lineKey);

        $databaseLine->saveValues($pairs);
    }
}