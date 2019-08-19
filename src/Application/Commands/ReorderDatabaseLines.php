<?php

namespace Thinktomorrow\Squanto\Application\Commands;

use Illuminate\Support\Facades\DB;
use Thinktomorrow\Squanto\Domain\DatabaseLine;
use Thinktomorrow\Squanto\Domain\Page;
use Thinktomorrow\Squanto\Domain\PageKey;

class ReorderDatabaseLines
{
    public function handle(PageKey $pageKey, array $keys)
    {
        $page = Page::findByKey($pageKey);

        foreach($keys as $i => $key){
            DatabaseLine::where('page_id', $page->id)
                        ->where('key', $key)
                        ->update(['sequence' => $i]);
        }
    }
}