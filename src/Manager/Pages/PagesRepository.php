<?php
declare(strict_types=1);

namespace Thinktomorrow\Squanto\Manager\Pages;

use Thinktomorrow\Squanto\Disk\ReadLanguageFolder;

final class PagesRepository
{
    /**
     * @var ReadLanguageFolder
     */
    private ReadLanguageFolder $readLanguageFolder;

    public function __construct(ReadLanguageFolder $readLanguageFolder)
    {
        $this->readLanguageFolder = $readLanguageFolder;
    }

    public function all(): Pages
    {
        $filenames = [];

        foreach(config('thinktomorrow.squanto.locales') as $locale) {
            $files = $this->readLanguageFolder->files($locale);
            foreach($files as $file){
                $filenames[] = $file['filename'];
            }
        }

        return Pages::fromFilenames(array_unique($filenames));
    }

    public function findBySlug(string $slug): Page
    {
        return Page::fromFilename($slug);
    }
}
