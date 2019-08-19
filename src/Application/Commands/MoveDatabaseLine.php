<?php

namespace Thinktomorrow\Squanto\Application\Commands;

use Thinktomorrow\Squanto\Domain\DatabaseLine;
use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\Squanto\Domain\Page;

class MoveDatabaseLine
{
    /**
     * Move or rename a database line
     *
     * @param LineKey $currentLineKey
     * @param LineKey $newLineKey
     */
    public function handle(LineKey $currentLineKey, LineKey $newLineKey)
    {
        if( ! $databaseLine = DatabaseLine::findByKey($currentLineKey)) return;

        if( ! $currentLineKey->getPageKey()->equals($newLineKey->getPageKey()))
        {
            $databaseLine->page_id = Page::findOrCreateByKey($newLineKey->getPageKey())->id;
        }

        $databaseLine->key = $newLineKey->get();
        $databaseLine->label = $newLineKey->getAsLabel();
        $databaseLine->save();
    }
}