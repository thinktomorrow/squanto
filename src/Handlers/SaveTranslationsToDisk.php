<?php

namespace Thinktomorrow\Squanto\Handlers;

use Thinktomorrow\Squanto\Domain\Trans;

class SaveTranslationsToDisk
{
    /**
     * @var WriteTranslationLineToDisk
     */
    private $writer;

    public function __construct(WriteTranslationLineToDisk $writer)
    {
        $this->writer = $writer;
    }

    public function handle($locale = null)
    {
        Trans::getFlattenedTranslationLines($locale)->each(function ($lines, $locale) {
            $this->writer->write($locale, $lines);
        });
    }

    public function clear($locale = null)
    {
        app(ClearCacheTranslations::class)->clear($locale);

        return $this;
    }
}
