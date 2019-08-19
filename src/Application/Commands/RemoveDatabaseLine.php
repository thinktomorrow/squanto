<?php

namespace Thinktomorrow\Squanto\Application\Commands;

use Thinktomorrow\Squanto\Domain\DatabaseLine;
use Thinktomorrow\Squanto\Domain\LineKey;

class RemoveDatabaseLine
{
    public function handle(LineKey $lineKey)
    {
        // remove line with all associated translations
        if( ! $databaseLine = DatabaseLine::findByKey($lineKey)) return;

        $databaseLine->translations()->delete();
        $databaseLine->delete();
    }
}