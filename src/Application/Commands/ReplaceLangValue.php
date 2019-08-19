<?php

namespace Thinktomorrow\Squanto\Application\Commands;

use Thinktomorrow\Squanto\Domain\LangFile;
use Thinktomorrow\Squanto\Domain\LineKey;

class ReplaceLangValue
{
    public function handle(LangFile $langFile, LineKey $lineKey, $value)
    {
        $langFile->replace($lineKey, $value)
                 ->save();
    }
}