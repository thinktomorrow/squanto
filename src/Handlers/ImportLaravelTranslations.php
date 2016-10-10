<?php

namespace Thinktomorrow\Squanto\Handlers;

use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Services\LaravelTranslationsReader;

class ImportLaravelTranslations
{
    private $reader;

    public function __construct(LaravelTranslationsReader $reader)
    {
        $this->reader = $reader;
    }

    public function import($locale)
    {
        $translations = $this->reader->read($locale)->flatten();

        $translations->each(function($value,$key) use($locale){
            Line::findOrCreateByKey($key)->saveValue($locale,$value);
        });
    }


}
