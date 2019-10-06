<?php

namespace Thinktomorrow\Squanto\Application\Commands;

use Thinktomorrow\Squanto\Domain\DatabaseLine;
use Thinktomorrow\Squanto\Domain\LineKey;

class AddDatabaseLine
{
    /**
     * If a value exists in the database, this is never replaced by this command.
     * With this flag set to true, this action will replace any database value.
     * @var bool
     */
    private $forceReplacement = false;

    public function handle(LineKey $lineKey, array $pairs)
    {
        if($existingDatabaseLine = DatabaseLine::findByKey($lineKey)) {
            if($this->forceReplacement) {
                $existingDatabaseLine->saveValues($pairs);
            }

            return;
        }

        $databaseLine = DatabaseLine::createFromKey($lineKey);

        $databaseLine->saveValues($pairs);
    }

    public function forceReplacement(): self
    {
        $this->forceReplacement = true;

        return $this;
    }
}
