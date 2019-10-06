<?php

namespace Thinktomorrow\Squanto\Application\Commands;

use Thinktomorrow\Squanto\Domain\DatabaseLine;
use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\Squanto\Exceptions\InvalidLineKeyException;

class RemoveDatabaseLine
{
    public function handle(LineKey $lineKey)
    {
        // remove line with all associated translations
        if( ! $databaseLine = DatabaseLine::findByKey($lineKey)) {
            throw new InvalidLineKeyException('Remove action aborted. No database entry found by key [' . $currentLineKey->get() . ']');
        }

        $databaseLine->translations()->delete();
        $databaseLine->delete();
    }
}
