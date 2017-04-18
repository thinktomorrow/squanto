<?php

namespace Thinktomorrow\Squanto\Application\Rename;

use Thinktomorrow\Squanto\Domain\Line;

final class RenameKey
{
    public function handle(Line $line, $key)
    {
        $currentPage = $line->page;

        $line->changeKey($key);

        // Empty pages will be removed
        if($currentPage->lines()->count() < 1) $currentPage->delete();
    }
}