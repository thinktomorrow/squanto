<?php

namespace Thinktomorrow\Squanto\Handlers;

use League\Flysystem\Filesystem;

class ImportLaravelTranslations
{
    /**
     * Local filesystem. Already contains the path to our translation files
     * e.g. storage/app/trans
     *
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function import()
    {
        // Read originals

        // Save to db
    }


}
